<?php
// // Bật error logging
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/cfd_debug.log');  // Tạo file log riêng
// error_reporting(E_ALL);

// Test log
// error_log("=== Log file created at: " . __DIR__ . '/cfd_debug.log');
class KanbanBoardController
{
    private $db_link;

    /**
     * Hàm khởi tạo, thiết lập kết nối cơ sở dữ liệu.
     */
    public function __construct()
    {
        global $db_link;
        if (!$db_link || !mysqli_ping($db_link)) {
            db_connect();
            global $db_link;
        }
        $this->db_link = $db_link;
    }
    public function get_velocity_chart_data($planId)
    {
        if (empty($planId)) {
            return ['success' => false, 'message' => 'Mã kế hoạch không được để trống.'];
        }

        // ✅ SỬA: Lấy thêm thông tin chi tiết về task
        $tasks_with_dates_query = "SELECT 
                                  t_master.lv001 as task_id,
                                  t_master.lv501 as task_code,
                                  t_master.lv004 as task_name,
                                  t_plan.lv501 as project_id,
                                  MIN(DATE(t_kanban.lv013)) as start_date,
                                  MIN(DATE(t_kanban.lv019)) as end_date,
                                  MIN(t_kanban.lv019) as end_datetime
                               FROM cr_lv0005 AS t_master
                               JOIN cr_lv0004 AS t_plan ON t_master.lv002 = t_plan.lv001
                               LEFT JOIN da_lh0003 AS t_kanban 
                                   ON t_master.lv501 = t_kanban.lv004 
                                   AND t_kanban.lv018 = t_plan.lv501
                               WHERE t_master.lv002 = ?
                               GROUP BY t_master.lv001, t_master.lv501, t_plan.lv501";

        $stmt_tasks = mysqli_prepare($this->db_link, $tasks_with_dates_query);
        mysqli_stmt_bind_param($stmt_tasks, "s", $planId);
        mysqli_stmt_execute($stmt_tasks);
        $result_tasks = mysqli_stmt_get_result($stmt_tasks);

        $tasks = [];
        $task_details = []; // ✅ THÊM: Chi tiết từng task
        $start_dates = [];
        $project_id = null;

        while ($row = mysqli_fetch_assoc($result_tasks)) {
            $task_id = $row['task_id'];
            $tasks[] = $task_id;
            $project_id = $row['project_id'];

            // ✅ THÊM: Lưu chi tiết task
            $task_details[$task_id] = [
                'task_code' => $row['task_code'],
                'task_name' => $row['task_name'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'end_datetime' => $row['end_datetime']
            ];

            if (!empty($row['start_date']) && $row['start_date'] != '0000-00-00') {
                $start_dates[] = $row['start_date'];
            }
        }
        mysqli_stmt_close($stmt_tasks);

        if (empty($tasks)) {
            return ['success' => true, 'data' => []];
        }

        $total = count($tasks);
        $min_start_date = !empty($start_dates) ? min($start_dates) : date('Y-m-d', strtotime('-8 weeks'));

        // 2. Lấy ngày hoàn thành
        $placeholders = str_repeat('?,', count($tasks) - 1) . '?';
        $done_query = "SELECT lv001 as task_id, DATE(lv005) as done_date, lv005 as done_datetime
                   FROM da_lh0009
                   WHERE lv001 IN ($placeholders) AND lv004=1 AND lv005 IS NOT NULL";
        $stmt_done = mysqli_prepare($this->db_link, $done_query);
        mysqli_stmt_bind_param($stmt_done, str_repeat('i', count($tasks)), ...$tasks);
        mysqli_stmt_execute($stmt_done);
        $result_done = mysqli_stmt_get_result($stmt_done);

        $done_dates = [];
        while ($row = mysqli_fetch_assoc($result_done)) {
            $task_id = $row['task_id'];
            $done_dates[$task_id] = $row['done_date'];
            // ✅ THÊM: Cập nhật thông tin hoàn thành vào task_details
            if (isset($task_details[$task_id])) {
                $task_details[$task_id]['completed_date'] = $row['done_date'];
                $task_details[$task_id]['completed_datetime'] = $row['done_datetime'];
            }
        }
        mysqli_stmt_close($stmt_done);

        // 3. Xác định khoảng tuần
        $start_date = $min_start_date ?: date('Y-m-d', strtotime('-8 weeks'));
        $end_date = date('Y-m-d');

        $weeks = $this->generate_week_dates($start_date, $end_date);

        // 4. Map công việc vào tuần hoàn thành
        $task_done_week = [];
        foreach ($done_dates as $task_id => $done_date) {
            foreach ($weeks as $idx => $week) {
                if ($done_date >= $week['start_date'] && $done_date <= $week['end_date']) {
                    $task_done_week[$task_id] = $idx;
                    break;
                }
            }
        }

        // 5. ✅ THÊM: Phân loại task theo trạng thái
        $current_date = date('Y-m-d');
        $task_status = [
            'completed' => [],
            'overdue' => [],
            'on_time' => [],
            'no_deadline' => []
        ];

        foreach ($task_details as $task_id => $detail) {
            if (isset($done_dates[$task_id])) {
                $task_status['completed'][] = $task_id;
            } elseif (empty($detail['end_date']) || $detail['end_date'] == '0000-00-00') {
                $task_status['no_deadline'][] = $task_id;
            } elseif ($detail['end_date'] < $current_date) {
                $task_status['overdue'][] = $task_id;
            } else {
                $task_status['on_time'][] = $task_id;
            }
        }

        // 6. Tính velocity data cho từng tuần
        $velocity_data = [];
        $cum_completed = 0;

        for ($i = 0; $i < count($weeks); $i++) {
            $week = $weeks[$i];

            // Đếm số công việc hoàn thành trong tuần này
            $completed_this_week = 0;
            $completed_tasks_this_week = [];
            foreach ($task_done_week as $task_id => $week_idx) {
                if ($week_idx === $i) {
                    $completed_this_week++;
                    $completed_tasks_this_week[] = $task_details[$task_id];
                }
            }
            $cum_completed += $completed_this_week;

            // Tính số công việc trễ hạn tại tuần này
            $overdue_count = 0;
            $overdue_tasks_this_week = [];
            $week_end_date = $week['end_date'];

            foreach ($task_details as $task_id => $detail) {
                if (
                    !isset($done_dates[$task_id]) &&
                    !empty($detail['end_date']) &&
                    $detail['end_date'] != '0000-00-00' &&
                    $detail['end_date'] < $week_end_date
                ) {
                    $overdue_count++;
                    $overdue_tasks_this_week[] = $detail;
                }
            }

            $velocity_data[] = [
                'week' => $week['label'],
                'total' => $total,
                'completed' => $cum_completed,
                'overdue' => $overdue_count,
                // ✅ THÊM: Chi tiết tasks trong tuần
                'completed_tasks' => $completed_tasks_this_week,
                'overdue_tasks' => array_slice($overdue_tasks_this_week, 0, 5) // Chỉ lấy 5 task đầu
            ];
        }

        return [
            'success' => true,
            'data' => $velocity_data,
            'summary' => [
                'total_tasks' => $total,
                'completed_count' => count($task_status['completed']),
                'overdue_count' => count($task_status['overdue']),
                'on_time_count' => count($task_status['on_time']),
                'no_deadline_count' => count($task_status['no_deadline'])
            ],
            'task_details' => array_slice($task_details, 0, 10, true), // Sample 10 tasks
            'meta' => [
                'plan_id' => $planId,
                'project_id' => $project_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'current_date' => $current_date,
                'total_weeks' => count($weeks)
            ]
        ];
    }
    public function get_cfd_chart_data($planId)
    {
        error_log("=== CFD Function Start ===");
        error_log("Plan ID: " . $planId);

        if (empty($planId)) {
            error_log("ERROR: Plan ID is empty");
            return ['success' => false, 'message' => 'Mã kế hoạch không được để trống.'];
        }

        // B1: Lấy danh sách công việc từ cr_lv0005
        $tasks = [];
        $tasks_query = "SELECT lv001 as task_id, lv501 as kanban_code 
                    FROM cr_lv0005 
                    WHERE lv002 = ?";

        error_log("Query 1: " . $tasks_query);

        $stmt_tasks = mysqli_prepare($this->db_link, $tasks_query);

        if (!$stmt_tasks) {
            error_log("ERROR: Prepare failed - " . mysqli_error($this->db_link));
            return ['success' => false, 'message' => 'Lỗi prepare query: ' . mysqli_error($this->db_link)];
        }

        mysqli_stmt_bind_param($stmt_tasks, "s", $planId);
        mysqli_stmt_execute($stmt_tasks);
        $result_tasks = mysqli_stmt_get_result($stmt_tasks);

        while ($row = mysqli_fetch_assoc($result_tasks)) {
            $tasks[] = $row;
        }
        mysqli_stmt_close($stmt_tasks);

        error_log("Tasks found: " . count($tasks));
        error_log("Tasks data: " . json_encode($tasks));

        if (empty($tasks)) {
            error_log("WARNING: No tasks found");
            return [
                'success' => true,
                'message' => 'Không có công việc nào trong kế hoạch này',
                'data' => []
            ];
        }

        // B2: Lấy task_ids và kanban_codes
        $task_ids = array_column($tasks, 'task_id');
        $kanban_codes = array_column($tasks, 'kanban_code');

        error_log("Task IDs: " . json_encode($task_ids));
        error_log("Kanban Codes: " . json_encode($kanban_codes));

        if (empty($task_ids) || empty($kanban_codes)) {
            error_log("ERROR: task_ids or kanban_codes is empty");
            return [
                'success' => true,
                'message' => 'Không có task ID hợp lệ',
                'data' => []
            ];
        }

        $placeholders = str_repeat('?,', count($kanban_codes) - 1) . '?';

        // ✅ B3: Lấy khoảng thời gian từ bảng da_lh0003 (lv013 và lv019)
        $date_query = "SELECT 
                    MIN(DATE(lv013)) as min_date, 
                    MAX(DATE(lv019)) as max_date 
                   FROM da_lh0003 
                   WHERE lv004 IN ($placeholders)
                   AND lv013 IS NOT NULL 
                   AND lv013 != '0000-00-00'";

        error_log("Date query: " . $date_query);
        error_log("Binding " . count($kanban_codes) . " kanban codes: " . implode(',', $kanban_codes));

        $stmt_date = mysqli_prepare($this->db_link, $date_query);

        if (!$stmt_date) {
            error_log("ERROR: Date query prepare failed - " . mysqli_error($this->db_link));
            $start_date = date('Y-m-d', strtotime('-12 weeks'));
            $end_date = date('Y-m-d');
        } else {
            $types = str_repeat('s', count($kanban_codes)); // lv004 là string (CV001, ALARM, etc.)
            error_log("Bind types: " . $types);

            mysqli_stmt_bind_param($stmt_date, $types, ...$kanban_codes);

            if (!mysqli_stmt_execute($stmt_date)) {
                error_log("ERROR: Date query execute failed - " . mysqli_stmt_error($stmt_date));
                $start_date = date('Y-m-d', strtotime('-12 weeks'));
                $end_date = date('Y-m-d');
            } else {
                $date_result = mysqli_stmt_get_result($stmt_date);
                $date_row = mysqli_fetch_assoc($date_result);

                error_log("Date row result: " . json_encode($date_row));

                $start_date = $date_row['min_date'] ?? date('Y-m-d', strtotime('-12 weeks'));
                $end_date = $date_row['max_date'] ?? date('Y-m-d');

                // Nếu không có ngày kết thúc, dùng ngày hiện tại
                if (empty($start_date) || $start_date === '0000-00-00') {
                    error_log("WARNING: Invalid start_date, using default");
                    $start_date = date('Y-m-d', strtotime('-12 weeks'));
                }
                if (empty($end_date) || $end_date === '0000-00-00') {
                    error_log("WARNING: Invalid end_date, using current date");
                    $end_date = date('Y-m-d');
                }
            }
            mysqli_stmt_close($stmt_date);
        }

        error_log("Date range: $start_date to $end_date");

        // B4: Tạo mảng các tuần
        $weeks = $this->generate_week_dates($start_date, $end_date);

        error_log("Weeks generated: " . count($weeks));
        if (count($weeks) > 0) {
            error_log("First week: " . json_encode($weeks[0]));
            error_log("Last week: " . json_encode($weeks[count($weeks) - 1]));
        } else {
            error_log("ERROR: No weeks generated!");
        }

        if (empty($weeks)) {
            return [
                'success' => true,
                'message' => 'Không thể tạo danh sách tuần',
                'data' => [],
                'debug' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'task_ids' => $task_ids
                ]
            ];
        }

        // B5: Tính toán trạng thái cho từng tuần
        $cfd_data = [];

        foreach ($weeks as $index => $week_data) {
            error_log("Processing week " . ($index + 1) . "/" . count($weeks) . ": " . $week_data['label']);

            $status_count = $this->calculate_task_status_for_date($task_ids, $week_data['end_date']);

            error_log("Week status: " . json_encode($status_count));

            $cfd_data[] = [
                'Ngay' => $week_data['label'],
                'Chưa bắt đầu' => $status_count['todo'],
                'Đang làm' => $status_count['in_progress'],
                'Hoàn thành' => $status_count['done']
            ];
        }

        error_log("CFD data points created: " . count($cfd_data));
        error_log("Final CFD data: " . json_encode($cfd_data));
        error_log("=== CFD Function End ===");

        return [
            'success' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $cfd_data,
            'meta' => [
                'total_tasks' => count($tasks),
                'task_ids' => $task_ids,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'total_weeks' => count($weeks),
                'total_data_points' => count($cfd_data)
            ]
        ];
    }

    /**
     * ✅ Sửa lại hàm calculate_task_status_for_date
     * - Worklog chỉ dùng để xác định "Đang làm"
     * - Không dùng worklog để lấy khoảng thời gian
     */
    private function calculate_task_status_for_date($task_ids, $target_date)
    {
        error_log("--- Calculate status for date: $target_date ---");
        error_log("Task IDs to check: " . json_encode($task_ids));

        if (empty($task_ids)) {
            error_log("ERROR: task_ids is empty!");
            return ['todo' => 0, 'in_progress' => 0, 'done' => 0];
        }

        $placeholders = str_repeat('?,', count($task_ids) - 1) . '?';

        // 1. Đếm công việc hoàn thành (từ bảng da_lh0009)
        $done_query = "SELECT COUNT(DISTINCT lv001) as count
                   FROM da_lh0009 
                   WHERE lv001 IN ($placeholders)
                   AND lv004 = 1
                   AND DATE(lv005) <= ?";

        error_log("Done query: $done_query");

        $stmt_done = mysqli_prepare($this->db_link, $done_query);
        $params_done = array_merge($task_ids, [$target_date]);
        $types_done = str_repeat('i', count($task_ids)) . 's';

        error_log("Done params: " . json_encode($params_done));
        error_log("Done types: $types_done");

        mysqli_stmt_bind_param($stmt_done, $types_done, ...$params_done);
        mysqli_stmt_execute($stmt_done);
        $result_done = mysqli_stmt_get_result($stmt_done);
        $done_count = (int)mysqli_fetch_assoc($result_done)['count'];
        mysqli_stmt_close($stmt_done);

        error_log("Done count: $done_count");

        // 2. ✅ Đếm công việc đang làm (có worklog NHƯNG chưa hoàn thành)
        $in_progress_query = "SELECT COUNT(DISTINCT lv002) as count
                          FROM cr_lv0090 
                          WHERE lv002 IN ($placeholders)
                          AND DATE(lv005) <= ?
                          AND lv002 NOT IN (
                              SELECT lv001 FROM da_lh0009 
                              WHERE lv004 = 1 
                              AND DATE(lv005) <= ?
                          )";

        error_log("In progress query: $in_progress_query");

        $stmt_progress = mysqli_prepare($this->db_link, $in_progress_query);
        $params_progress = array_merge($task_ids, [$target_date, $target_date]);
        $types_progress = str_repeat('i', count($task_ids)) . 'ss';

        error_log("Progress params: " . json_encode($params_progress));
        error_log("Progress types: $types_progress");

        mysqli_stmt_bind_param($stmt_progress, $types_progress, ...$params_progress);
        mysqli_stmt_execute($stmt_progress);
        $result_progress = mysqli_stmt_get_result($stmt_progress);
        $in_progress_count = (int)mysqli_fetch_assoc($result_progress)['count'];
        mysqli_stmt_close($stmt_progress);

        error_log("In progress count: $in_progress_count");

        // 3. Tính todo (Chưa bắt đầu = không có worklog và chưa hoàn thành)
        $total_tasks = count($task_ids);
        $todo_count = max(0, $total_tasks - $done_count - $in_progress_count);

        error_log("Total: $total_tasks, Done: $done_count, Progress: $in_progress_count, Todo: $todo_count");

        return [
            'todo' => $todo_count,
            'in_progress' => $in_progress_count,
            'done' => $done_count
        ];
    }
    /**
     * Tạo danh sách các tuần từ ngày bắt đầu đến ngày kết thúc
     * Mỗi tuần bắt đầu từ Thứ Hai và kết thúc vào Chủ Nhật
     * @param string $start_date - Ngày bắt đầu (Y-m-d)
     * @param string $end_date - Ngày kết thúc (Y-m-d)
     * @return array - Mảng các tuần với thông tin start_date, end_date, label
     */
    private function generate_week_dates($start_date, $end_date)
    {
        $weeks = [];

        // Tìm ngày Thứ Hai đầu tiên (bắt đầu tuần)
        $current = strtotime($start_date);
        $day_of_week = date('N', $current); // 1 (Thứ Hai) đến 7 (Chủ Nhật)

        // Lùi về Thứ Hai gần nhất nếu không phải Thứ Hai
        if ($day_of_week != 1) {
            $current = strtotime('last Monday', $current);
        }

        $end_timestamp = strtotime($end_date);

        while ($current <= $end_timestamp) {
            $week_start = date('Y-m-d', $current);
            $week_end = date('Y-m-d', strtotime('+6 days', $current));

            // Đảm bảo không vượt quá ngày kết thúc
            if (strtotime($week_end) > $end_timestamp) {
                $week_end = $end_date;
            }

            $weeks[] = [
                'start_date' => $week_start,
                'end_date' => $week_end,
                'label' => $this->format_week_label($week_start, $week_end)
            ];

            // Chuyển sang tuần tiếp theo (Thứ Hai)
            $current = strtotime('+7 days', $current);
        }

        return $weeks;
    }

    /**
     * Format label cho tuần
     * @param string $start - Ngày bắt đầu tuần
     * @param string $end - Ngày kết thúc tuần
     * @return string - Label dạng "Tuần 01/01 - 07/01" hoặc "01-07/01"
     */
    private function format_week_label($start, $end)
    {
        $start_day = date('d', strtotime($start));
        $start_month = date('m', strtotime($start));
        $end_day = date('d', strtotime($end));
        $end_month = date('m', strtotime($end));
        $end_year = date('Y', strtotime($end));

        // Nếu cùng tháng: "01-07/01"
        if ($start_month == $end_month) {
            return "Tuần {$start_day}-{$end_day}/{$start_month}";
        }
        // Nếu khác tháng: "28/12 - 03/01"
        else {
            return "Tuần {$start_day}/{$start_month} - {$end_day}/{$end_month}";
        }
    }



    /**
     * Phiên bản nâng cao - Lấy dữ liệu CFD theo tuần với tùy chọn
     */
    public function get_cfd_chart_data_advanced($planId, $startDate = null, $endDate = null, $groupBy = 'week')
    {
        if (empty($planId)) {
            return ['success' => false, 'message' => 'Mã kế hoạch không được để trống.'];
        }

        // Lấy danh sách công việc
        $tasks = [];
        $tasks_query = "SELECT lv001 as task_id, lv501 as kanban_code 
                    FROM cr_lv0005 
                    WHERE lv002 = ?";

        $stmt_tasks = mysqli_prepare($this->db_link, $tasks_query);
        mysqli_stmt_bind_param($stmt_tasks, "s", $planId);
        mysqli_stmt_execute($stmt_tasks);
        $result_tasks = mysqli_stmt_get_result($stmt_tasks);

        while ($row = mysqli_fetch_assoc($result_tasks)) {
            $tasks[] = $row;
        }
        mysqli_stmt_close($stmt_tasks);

        if (empty($tasks)) {
            return [
                'success' => true,
                'message' => 'Không có công việc nào trong kế hoạch này',
                'data' => []
            ];
        }

        $task_ids = array_column($tasks, 'task_id');

        // Xử lý khoảng thời gian
        if (!$startDate || !$endDate) {
            $placeholders = str_repeat('?,', count($task_ids) - 1) . '?';
            $date_query = "SELECT MIN(DATE(lv015)) as min_date, MAX(DATE(lv015)) as max_date 
                       FROM cr_lv0090 
                       WHERE lv002 IN ($placeholders)";

            $stmt_date = mysqli_prepare($this->db_link, $date_query);
            mysqli_stmt_bind_param($stmt_date, str_repeat('i', count($task_ids)), ...$task_ids);
            mysqli_stmt_execute($stmt_date);
            $date_result = mysqli_stmt_get_result($stmt_date);
            $date_row = mysqli_fetch_assoc($date_result);
            mysqli_stmt_close($stmt_date);

            $startDate = $date_row['min_date'] ?? date('Y-m-d', strtotime('-12 weeks'));
            $endDate = $date_row['max_date'] ?? date('Y-m-d');
        }

        // Tạo danh sách các khoảng thời gian
        if ($groupBy === 'week') {
            $periods = $this->generate_week_dates($startDate, $endDate);
        } else {
            // Có thể mở rộng cho 'day', 'month'
            $periods = $this->generate_week_dates($startDate, $endDate);
        }

        // Tính toán dữ liệu
        $cfd_data = [];
        foreach ($periods as $period) {
            $status_count = $this->calculate_task_status_advanced($task_ids, $period['end_date']);

            $cfd_data[] = array_merge(
                ['Ngay' => $period['label']],
                $status_count
            );
        }

        return [
            'success' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $cfd_data,
            'meta' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_periods' => count($periods),
                'total_tasks' => count($task_ids),
                'group_by' => $groupBy
            ]
        ];
    }

    /**
     * Tính toán trạng thái nâng cao với nhiều giai đoạn
     */
    private function calculate_task_status_advanced($task_ids, $target_date)
    {
        if (empty($task_ids)) {
            return [
                'Backlog' => 0,
                'To Do' => 0,
                'In Progress' => 0,
                'Review' => 0,
                'Done' => 0
            ];
        }

        $placeholders = str_repeat('?,', count($task_ids) - 1) . '?';

        // Đếm Done
        $done_query = "SELECT COUNT(DISTINCT lv001) as count
                   FROM da_lh0009 
                   WHERE lv001 IN ($placeholders)
                   AND lv004 = 1
                   AND DATE(lv005) <= ?
                  ";

        $stmt_done = mysqli_prepare($this->db_link, $done_query);
        $params = array_merge($task_ids, [$target_date]);
        $types = str_repeat('i', count($task_ids)) . 's';
        mysqli_stmt_bind_param($stmt_done, $types, ...$params);
        mysqli_stmt_execute($stmt_done);
        $done_count = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_done))['count'];
        mysqli_stmt_close($stmt_done);

        // Đếm In Progress
        $progress_query = "SELECT COUNT(DISTINCT lv002) as count
                       FROM cr_lv0090 
                       WHERE lv002 IN ($placeholders)
                       AND DATE(lv015) <= ?
                       AND lv002 NOT IN (
                           SELECT lv001 FROM da_lh0009 
                           WHERE lv004 = 1 AND DATE(lv005) <= ? 
                       )
                      ";

        $stmt_progress = mysqli_prepare($this->db_link, $progress_query);
        $params_progress = array_merge($task_ids, [$target_date, $target_date]);
        $types_progress = str_repeat('i', count($task_ids)) . 'ss';
        mysqli_stmt_bind_param($stmt_progress, $types_progress, ...$params_progress);
        mysqli_stmt_execute($stmt_progress);
        $progress_count = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_progress))['count'];
        mysqli_stmt_close($stmt_progress);

        // Tính To Do
        $total = count($task_ids);
        $todo_count = max(0, $total - $done_count - $progress_count);

        return [
            'Backlog' => 0,
            'To Do' => $todo_count,
            'In Progress' => $progress_count,
            'Review' => 0,
            'Done' => $done_count
        ];
    }
    public function get_burndown_chart_data($planId)
    {
        if (empty($planId)) {
            return ['success' => false, 'message' => 'Mã kế hoạch không được để trống.'];
        }

        // Câu truy vấn SQL trực tiếp từ yêu cầu của bạn
        $query = "
            SELECT 
                d.Ngay, 
                COUNT(cv.lv001) - SUM(
                    CASE 
                        WHEN da9.lv004 = 1 AND DATE(da9.lv005) <= d.Ngay THEN 1 
                        ELSE 0 
                    END
                ) AS CongViecConLai
            FROM (
                SELECT DATE_ADD(start_date.NgayBatDau, INTERVAL seqs.seq DAY) AS Ngay
                FROM (
                    SELECT DATE(MIN(da3.lv013)) AS NgayBatDau
                    FROM da_lh0003 da3
                    JOIN cr_lv0005 cv2 ON da3.lv004 = cv2.lv501
                    WHERE cv2.lv002 = ?
                ) AS start_date
                JOIN (
                    SELECT 0 AS seq UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL
                    SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
                    SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL
                    SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
                    SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL
                    SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
                    SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL
                    SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
                    SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL
                    SELECT 45
                ) seqs
                WHERE DATE_ADD(start_date.NgayBatDau, INTERVAL seqs.seq DAY) <= CURDATE()
            ) AS d
            CROSS JOIN cr_lv0005 cv
            LEFT JOIN da_lh0009 da9 ON cv.lv001 = da9.lv001
            WHERE cv.lv002 = ?
            GROUP BY d.Ngay
            ORDER BY d.Ngay
        ";

        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn: ' . mysqli_error($this->db_link)];
        }

        // Gắn planId vào cả hai placeholder
        mysqli_stmt_bind_param($stmt, "ss", $planId, $planId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['CongViecConLai'] = (int)$row['CongViecConLai'];
                $data[] = $row;
            }
        }
        mysqli_stmt_close($stmt);

        // Kiểm tra nếu không có dữ liệu trả về
        if (empty($data)) {
            // Chạy một truy vấn phụ để kiểm tra xem kế hoạch có ngày bắt đầu hay không
            $check_start_date_query = "SELECT MIN(da3.lv013) as StartDate FROM da_lh0003 da3 JOIN cr_lv0005 cv2 ON da3.lv004 = cv2.lv501 WHERE cv2.lv002 = ?";
            $check_stmt = mysqli_prepare($this->db_link, $check_start_date_query);
            mysqli_stmt_bind_param($check_stmt, "s", $planId);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            $start_date_row = mysqli_fetch_assoc($check_result);
            mysqli_stmt_close($check_stmt);

            if ($start_date_row['StartDate'] === null) {
                return ['success' => false, 'message' => 'Không tìm thấy ngày bắt đầu cho kế hoạch này. Vui lòng kiểm tra lại mã kế hoạch hoặc dữ liệu công việc.'];
            }

            // Nếu có ngày bắt đầu nhưng không có dòng nào, có thể là do ngày bắt đầu trong tương lai
            return ['success' => true, 'data' => []];
        }

        return ['success' => true, 'data' => $data];
    }
    function export_worklog_pdf($taskId, $db_link)
    {
        // Lấy dữ liệu worklog
        $query = "SELECT lv005 AS execution_datetime, lv004 AS work_content, lv008 AS user_id 
              FROM cr_lv0090 WHERE lv002 = ? ORDER BY lv005 ASC";
        $stmt = mysqli_prepare($db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $taskId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $worklogs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $worklogs[] = $row;
        }
        mysqli_stmt_close($stmt);

        // Tạo nội dung HTML cho PDF
        $html = '
    <h2 style="text-align:center;">Work Log Công Việc: ' . htmlspecialchars($taskId) . '</h2>
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:#f0f0f0;">
                <th>Ngày thực hiện</th>
                <th>Nội dung</th>
                <th>Người thực hiện</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($worklogs as $wl) {
            $html .= '
            <tr>
                <td>' . date('d/m/Y H:i', strtotime($wl['execution_datetime'])) . '</td>
                <td>' . htmlspecialchars($wl['work_content']) . '</td>
                <td>' . htmlspecialchars($wl['user_id']) . '</td>
            </tr>
        ';
        }
        $html .= '</tbody></table>';

        // Xuất ra PDF
        $mpdf = new \Mpdf\Mpdf(['utf-8', 'A4']);
        $mpdf->SetTitle('Work Log - ' . $taskId);
        $mpdf->WriteHTML($html);

        // Xuất file PDF về browser (download)
        $filename = 'worklog_' . $taskId . '.pdf';
        $mpdf->Output($filename, 'D'); // 'D' = download, 'I' = inline

        // Nếu muốn trả về nội dung PDF (cho API), dùng: $mpdf->Output('', 'S');
    }
    // ===================================================================
    // ==                     CÁC HÀM LẤY DỮ LIỆU BẢNG                   ==
    // ===================================================================
    public function update_subtask_status_data($subtaskId, $isCompleted)
    {
        if (empty($subtaskId)) {
            return ['success' => false, 'message' => 'Thiếu ID công việc con.'];
        }

        $query = "UPDATE da_kanban_subtasks SET is_completed = ?, completed_at = ? WHERE subtask_id = ?";

        $completedAt = $isCompleted ? date('Y-m-d H:i:s') : null;
        $status = $isCompleted ? 1 : 0;

        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "isi", $status, $completedAt, $subtaskId);

        if (mysqli_stmt_execute($stmt)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật trạng thái.'];
        }
    }
    public function get_subtasks_data($parentTaskId)
    {
        if (empty($parentTaskId)) return [];

        $subtasks = [];
        // Truy vấn vào bảng mới da_kanban_subtasks
        $query = "SELECT 
                    s.subtask_id as id,
                    s.title,
                    s.assignee_id as assigneeId,
                    u.lv002 as assigneeName,
                    s.is_completed
                FROM da_kanban_subtasks AS s
                LEFT JOIN hr_lv0020 AS u ON s.assignee_id = u.lv001
                WHERE s.parent_lv001 = ?
                ORDER BY s.created_at ASC";

        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "i", $parentTaskId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $subtasks[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $subtasks;
    }
    public function get_departments_by_project_data($projectId)
    {
        if (empty($projectId)) {
            return [];
        }

        $all_dept_ids = [];

        // Bước 1: Lấy tất cả các chuỗi ID phòng ban từ CSDL
        $query_ids = "SELECT DISTINCT lv002 as department_ids_str 
                  FROM da_lh0007 
                  WHERE lv018 = ? AND (lv008 = 0 OR lv008 IS NULL)";

        $stmt_ids = mysqli_prepare($this->db_link, $query_ids);
        mysqli_stmt_bind_param($stmt_ids, "s", $projectId);
        mysqli_stmt_execute($stmt_ids);
        $result_ids = mysqli_stmt_get_result($stmt_ids);

        // Bước 2: Dùng PHP để tách chuỗi và gom thành một mảng ID duy nhất
        while ($row = mysqli_fetch_assoc($result_ids)) {
            $ids_in_row = explode(',', $row['department_ids_str']);
            foreach ($ids_in_row as $id) {
                $trimmed_id = trim($id);
                if (!empty($trimmed_id) && !in_array($trimmed_id, $all_dept_ids)) {
                    $all_dept_ids[] = $trimmed_id;
                }
            }
        }
        mysqli_stmt_close($stmt_ids);

        if (empty($all_dept_ids)) {
            return [];
        }

        // Bước 3: Lấy thông tin tên phòng ban từ danh sách ID đã có
        $departments = [];
        // Tạo placeholders cho câu lệnh IN (...)
        $placeholders = implode(',', array_fill(0, count($all_dept_ids), '?'));
        $types = str_repeat('s', count($all_dept_ids));

        $query_names = "SELECT lv001 as id, lv003 as name 
                    FROM hr_lv0002 
                    WHERE lv001 IN ($placeholders) ORDER BY lv003 ASC";

        $stmt_names = mysqli_prepare($this->db_link, $query_names);
        mysqli_stmt_bind_param($stmt_names, $types, ...$all_dept_ids);
        mysqli_stmt_execute($stmt_names);
        $result_names = mysqli_stmt_get_result($stmt_names);

        while ($row = mysqli_fetch_assoc($result_names)) {
            $departments[] = $row;
        }
        mysqli_stmt_close($stmt_names);

        return $departments;
    }
    public function get_gantt_data($projectId)
    {
        // Tương tự như get_board_data, nhưng chỉ lấy các trường cần thiết cho Gantt
        // Có thể gom theo cột (giai đoạn)
        $gantt_tasks = [];
        $tasks_query = "SELECT t_kanban.lv001 as id, t_kanban.lv005 as title, t_kanban.lv003 as columnId,
                           t_kanban.lv013 as startDate, t_kanban.lv019 as endDate, t_master.lv006 as assigneeId
                    FROM da_lh0003 AS t_kanban
                    JOIN cr_lv0005 AS t_master ON t_kanban.lv004 = t_master.lv501
                    WHERE t_kanban.lv018 = ?";
        $stmt = mysqli_prepare($this->db_link, $tasks_query);
        mysqli_stmt_bind_param($stmt, "s", $projectId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $gantt_tasks[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $gantt_tasks;
    }
    /**
     * Lấy toàn bộ dữ liệu cần thiết để hiển thị Kanban board theo dự án.
     */
    public function get_board_data($projectId)
    {
        if ($projectId <= 0) {
            return ['users' => [], 'columns' => []];
        }

        // Lấy danh sách Users
        $users = [];
        $users_query = "SELECT lv001 as id, lv002 as name FROM hr_lv0020";
        $users_result = db_query($users_query);
        $colors = ['luxury-gradient', 'luxury-gradient-2', 'luxury-gradient-3', 'luxury-gradient-4', 'luxury-gradient-5'];
        $color_index = 0;
        while ($user_row = db_fetch_array($users_result)) {
            $user_row['initials'] = $this->getInitials($user_row['name']);
            $user_row['color'] = $colors[$color_index % count($colors)];
            $users[] = $user_row;
            $color_index++;
        }

        // 1. Lấy các cột thuộc dự án này
        $columns = [];
        $columns_query = "SELECT t2.lv001 as id, t2.lv002 as title 
                          FROM da_lh0005 as t1
                          JOIN da_lh0004 as t2 ON t1.lv002 = t2.lv001
                          WHERE t1.lv001 = ? 
                          ORDER BY t1.lv003 ASC";
        $stmt_cols = mysqli_prepare($this->db_link, $columns_query);
        mysqli_stmt_bind_param($stmt_cols, "i", $projectId);
        mysqli_stmt_execute($stmt_cols);
        $columns_result = mysqli_stmt_get_result($stmt_cols);
        while ($row = mysqli_fetch_assoc($columns_result)) {
            $columns[] = ['id' => $row['id'], 'title' => $row['title'], 'tasks' => []];
        }
        mysqli_stmt_close($stmt_cols);

        // 2. Lấy các công việc thuộc dự án
        $tasks_query = "SELECT DISTINCT
                        t_master.lv001 as id,
                        t_kanban.lv001 as kanban_task_id, -- Lấy thêm ID này để truy vấn phòng ban
                        t_kanban.lv003 as columnId,
                        t_kanban.lv004 as taskId,
                        t_kanban.lv005 as title,
                        t_kanban.lv007 as description,
                        t_master.lv006 as assigneeId,
                        t_kanban.lv017 as evaluation_status,
                        t_kanban.lv019 as endDate,
                        t_kanban.lv013 as startDate,
                        (SELECT COUNT(*) FROM da_lh0009 WHERE lv001 = t_master.lv001 AND lv004 = 1) as completed_dept_count
                    FROM 
                        da_lh0003 AS t_kanban
                    JOIN 
                        cr_lv0005 AS t_master ON t_kanban.lv004 = t_master.lv501
                    WHERE 
                        t_kanban.lv018 = ?";

        $stmt_tasks = mysqli_prepare($this->db_link, $tasks_query);
        mysqli_stmt_bind_param($stmt_tasks, "s", $projectId);
        mysqli_stmt_execute($stmt_tasks);
        $tasks_result = mysqli_stmt_get_result($stmt_tasks);
        $tasks = [];
        while ($task_row = mysqli_fetch_assoc($tasks_result)) {
            $tasks[] = $task_row;
        }
        mysqli_stmt_close($stmt_tasks);

        // BƯỚC 2.1: DÙNG PHP ĐỂ TÍNH CHÍNH XÁC `total_dept_count` CHO MỖI TASK
        foreach ($tasks as &$task) { // Dùng tham chiếu '&' để cập nhật trực tiếp
            $all_dept_ids = [];
            $kanban_task_id = $task['id'];
            $task['project_id'] = $projectId; // ✅ thêm project_id để hiển thị ở frontend

            // 1️⃣ Lấy lv501 từ bảng cr_lv0004
            $sql_lv501 = "SELECT lv501 FROM cr_lv0005 WHERE lv001 = ?";
            $stmt_lv501 = mysqli_prepare($this->db_link, $sql_lv501);
            mysqli_stmt_bind_param($stmt_lv501, "i", $kanban_task_id);
            mysqli_stmt_execute($stmt_lv501);
            $result_lv501 = mysqli_stmt_get_result($stmt_lv501);
            $row_lv501 = mysqli_fetch_assoc($result_lv501);
            mysqli_stmt_close($stmt_lv501);

            $lv501 = $row_lv501['lv501'] ?? null;
            if (!$lv501) {
                $task['total_dept_count'] = 0;
                continue;
            }

            // 2️⃣ Lấy lv004 từ bảng da_lh0004 dựa theo lv501
            $sql_lv001 = "SELECT lv001 FROM da_lh0003 WHERE lv004 = ? and lv018 = ?";
            $stmt_lv001 = mysqli_prepare($this->db_link, $sql_lv001);
            mysqli_stmt_bind_param($stmt_lv001, "ss", $lv501, $projectId);
            mysqli_stmt_execute($stmt_lv001);
            $result_lv001 = mysqli_stmt_get_result($stmt_lv001);
            $row_lv001 = mysqli_fetch_assoc($result_lv001);
            mysqli_stmt_close($stmt_lv001);

            $lv001 = $row_lv001['lv001'] ?? null;
            if (!$lv001) {
                $task['total_dept_count'] = 0;
                continue;
            }

            // 3️⃣ Truy vấn bảng da_lh0007 để lấy danh sách phòng ban (lv002)
            $dept_query = "SELECT lv002 AS department_ids_str 
                   FROM da_lh0007 
                   WHERE lv004 = ? AND lv018 = ?";
            $stmt_dept = mysqli_prepare($this->db_link, $dept_query);
            mysqli_stmt_bind_param($stmt_dept, "is", $lv001, $projectId);
            mysqli_stmt_execute($stmt_dept);
            $dept_result = mysqli_stmt_get_result($stmt_dept);

            while ($dept_row = mysqli_fetch_assoc($dept_result)) {
                $ids_in_row = explode(',', $dept_row['department_ids_str']);
                foreach ($ids_in_row as $id) {
                    $trimmed_id = trim($id);
                    if (!empty($trimmed_id) && !in_array($trimmed_id, $all_dept_ids)) {
                        $all_dept_ids[] = $trimmed_id;
                    }
                }
            }
            mysqli_stmt_close($stmt_dept);

            // 4️⃣ Gán số lượng phòng ban duy nhất đã đếm được vào task
            $task['total_dept_count'] = count($all_dept_ids);
        }
        unset($task); // Hủy tham chiếu
        // sau đó trả về toàn bộ $tasks

        // 3. Lấy icon đánh giá
        $eval_icons = [];
        $icons_query = "SELECT lv005 as status, lv005 as text, lv006 as icon, lv007 as color 
                        FROM da_lh0006 
                        WHERE lv018 = ? OR lv018 = '0'";
        $stmt_icons = mysqli_prepare($this->db_link, $icons_query);
        mysqli_stmt_bind_param($stmt_icons, "s", $projectId);
        mysqli_stmt_execute($stmt_icons);
        $icons_result = mysqli_stmt_get_result($stmt_icons);
        while ($icon_row = mysqli_fetch_assoc($icons_result)) {
            $eval_icons[] = $icon_row;
        }
        mysqli_stmt_close($stmt_icons);

        // 4. Gán task vào các cột
        foreach ($tasks as $task) {
            foreach ($columns as &$column) {
                if ($column['id'] == $task['columnId']) {
                    $column['tasks'][] = $task;
                    break;
                }
            }
        }
        unset($column);

        return [
            'users' => $users,
            'columns' => $columns,
            'evaluation_icons' => $eval_icons
        ];
    }

    /**
     * Lấy dữ liệu bảng đã được lọc theo dự án và phòng ban.
     */
    public function get_filtered_board_data($projectId, $departmentId, $userId, $user_role) // ✅ Thêm $user_role
    {
        if (empty($projectId)) {
            return ['success' => false, 'message' => 'Thiếu ID Dự án.'];
        }

        // ✅ NẾU LÀ MANAGER VÀ KHÔNG CÓ DEPARTMENT ID, TỰ ĐỘNG LẤY DEPARTMENT CỦA MANAGER
        if (empty($departmentId) && $user_role === 'manager' && !empty($userId)) {
            $dept_query = "SELECT lv029 as departmentId FROM hr_lv0020 WHERE lv001 = ? LIMIT 1";
            $stmt_dept = mysqli_prepare($this->db_link, $dept_query);
            if ($stmt_dept) {
                mysqli_stmt_bind_param($stmt_dept, "s", $userId);
                mysqli_stmt_execute($stmt_dept);
                $dept_result = mysqli_stmt_get_result($stmt_dept);
                if ($dept_row = mysqli_fetch_assoc($dept_result)) {
                    $departmentId = $dept_row['departmentId'];
                }
                mysqli_stmt_close($stmt_dept);
            }
        }

        // Kiểm tra lại sau khi đã cố gắng lấy departmentId
        if (empty($departmentId)) {
            return ['success' => false, 'message' => 'Thiếu ID Phòng ban.'];
        }

        $users = [];
        $planId = null;

        // Bước 1: Dựa vào projectId (lv501), lấy mã kế hoạch (lv002) từ bảng cr_lv0004
        $plan_query = "SELECT lv001 FROM cr_lv0004 WHERE lv501 = ?";
        $stmt_plan = mysqli_prepare($this->db_link, $plan_query);
        if ($stmt_plan) {
            mysqli_stmt_bind_param($stmt_plan, "s", $projectId);
            mysqli_stmt_execute($stmt_plan);
            $plan_result = mysqli_stmt_get_result($stmt_plan);
            if ($plan_row = mysqli_fetch_assoc($plan_result)) {
                $planId = $plan_row['lv001'];
            }
            mysqli_stmt_close($stmt_plan);
        }

        // Bước 2: Nếu tìm thấy mã kế hoạch, dùng nó để lấy danh sách nhân viên được phân công
        if ($planId) {
            $users_query = "SELECT 
                            emp.lv001 AS id, 
                            emp.lv002 AS name 
                        FROM 
                            hr_lv0020 AS emp
                        INNER JOIN 
                            da_lh0014 AS assign ON emp.lv001 = assign.lv003
                        WHERE 
                            assign.lv002 = ? and emp.lv029 = ?";
            
            $stmt_users = mysqli_prepare($this->db_link, $users_query);
            if ($stmt_users) {
                // Bind tham số $planId vào câu lệnh
                mysqli_stmt_bind_param($stmt_users, "ss", $planId, $departmentId);
                mysqli_stmt_execute($stmt_users);
                $users_result = mysqli_stmt_get_result($stmt_users);

                $colors = ['luxury-gradient', 'luxury-gradient-2', 'luxury-gradient-3', 'luxury-gradient-4', 'luxury-gradient-5'];
                $color_index = 0;
                while ($user_row = mysqli_fetch_assoc($users_result)) {
                    $user_row['initials'] = $this->getInitials($user_row['name']);
                    $user_row['color'] = $colors[$color_index % count($colors)];
                    $users[] = $user_row;
                    $color_index++;
                }
                mysqli_stmt_close($stmt_users);
            }
        }

        $columns = [];
        $column_ids = [];

        // SỬA LẠI CÂU QUERY LẤY CỘT ĐỂ DÙNG FIND_IN_SET VÀ NHẬN BIẾT LOẠI CỘT
        // Loại trừ cột Done (id = 7) khỏi query chính để xử lý riêng
        $columns_query = "SELECT DISTINCT 
                    t_col.lv002 AS id,              
                    t_stage.lv002 AS title,         
                    COALESCE(t_dept_map.lv008, 0) AS is_done_column
                    
                FROM da_lh0005 AS t_col            
                
                JOIN da_lh0004 AS t_stage 
                    ON t_col.lv002 = t_stage.lv001

                -- Vẫn LEFT JOIN để lấy trạng thái Done (nếu có cấu hình)
                LEFT JOIN da_lh0007 AS t_dept_map 
                    ON t_col.lv002 = t_dept_map.lv003       
                    AND t_dept_map.lv018 = t_col.lv001      

                WHERE 
                    t_col.lv001 = ?             -- Param 1: Project ID
                    AND t_col.lv004 = ?         -- Param 2: Department ID (QUAN TRỌNG)
                    AND t_col.lv002 != 7        
                
                ORDER BY t_col.lv003 ASC";

        // $columns_query1 = "SELECT DISTINCT t_stage.lv001 AS id, t_stage.lv002 AS title, t_dept_map.lv008 as is_done_column
        //               FROM da_lh0004 AS t_stage
        //               JOIN da_lh0007 AS t_dept_map ON t_stage.lv001 = t_dept_map.lv003
        //               WHERE t_dept_map.lv018 = ? AND FIND_IN_SET(?, t_dept_map.lv002) > 0 
        //               AND t_dept_map.lv008 != 1 AND t_stage.lv001 != 7
        //               ORDER BY t_dept_map.lv003 ASC ";

        $stmt_cols = mysqli_prepare($this->db_link, $columns_query);
        mysqli_stmt_bind_param($stmt_cols, "ss", $projectId, $departmentId);
        mysqli_stmt_execute($stmt_cols);
        $columns_result = mysqli_stmt_get_result($stmt_cols);
        while ($row = mysqli_fetch_assoc($columns_result)) {
            if (!in_array($row['id'], $column_ids)) {
                // Thêm tất cả các cột không phải DONE
                $columns[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'tasks' => [],
                    'is_done_column' => $row['is_done_column']
                ];
                $column_ids[] = $row['id'];
            }
        }
        mysqli_stmt_close($stmt_cols);

        // Luôn thêm cột DONE (id = 7) vào cuối nếu có
        $done_column_query = "SELECT DISTINCT t_stage.lv001 AS id, t_stage.lv002 AS title
                        FROM da_lh0004 AS t_stage
                        JOIN da_lh0007 AS t_dept_map ON t_stage.lv001 = t_dept_map.lv003
                        WHERE t_dept_map.lv018 = ? AND FIND_IN_SET(?, t_dept_map.lv002) > 0 
                        AND t_stage.lv001 = 7 LIMIT 1";

        $stmt_done = mysqli_prepare($this->db_link, $done_column_query);
        mysqli_stmt_bind_param($stmt_done, "ss", $projectId, $departmentId);
        mysqli_stmt_execute($stmt_done);
        $done_result = mysqli_stmt_get_result($stmt_done);
        if ($done_row = mysqli_fetch_assoc($done_result)) {
            if (!in_array($done_row['id'], $column_ids)) {
                $columns[] = ['id' => $done_row['id'], 'title' => $done_row['title'], 'tasks' => [], 'is_done_column' => 1];
            }
        }
        mysqli_stmt_close($stmt_done);

        $tasks = [];

        // TRUY VẤN 1: Lấy các công việc ĐANG HOẠT ĐỘNG
        // (Giữ nguyên logic lồng nhau lv003 và lv009)
        $active_tasks_query = "
                    SELECT 
                t_kanban.lv001 AS kanbanTaskId, 
                t_master.lv001 AS id, 
                t_kanban.lv004 AS taskId, 
                t_kanban.lv005 AS title, 
                t_kanban.lv007 AS description,
                t_kanban.lv013 AS startDate,
                t_kanban.lv019 AS endDate,
                -- THAY ĐỔI 1: Lấy thông tin từ bảng cột da_lh0005
                t_column.lv002 AS columnId, -- Giả sử lv002 là tên cột
                
                -- Tính toán ID người thực hiện (Giữ nguyên logic của bạn)
                COALESCE(
                    (SELECT sa.lv004 
                    FROM da_lh0008 sa 
                    WHERE sa.lv001 = t_master.lv001 
                    AND sa.lv002 = t_mapping.lv003 
                    AND FIND_IN_SET(?, sa.lv003) 
                    LIMIT 1),
                    t_kanban.lv016
                ) AS assigneeId,

                -- THAY ĐỔI 2: Lấy tên người thực hiện thông qua JOIN (tránh lỗi alias)
                assignee_user.lv002 AS assigneeName,
                
                0 AS is_completed, 
                t_kanban.lv017 AS evaluation_status,
                eval_icon.lv006 AS evaluation_icon, 
                eval_icon.lv007 AS evaluation_color

            FROM cr_lv0005 AS t_master

            -- Join Project
            JOIN cr_lv0004 AS t_project 
                ON t_project.lv001 = t_master.lv002 

            -- Join Kanban Info
            JOIN da_lh0003 AS t_kanban 
                ON t_master.lv501 = t_kanban.lv004
                AND t_kanban.lv018 = t_project.lv501 

            -- Join Mapping (Công việc - Cột)
            JOIN da_lh0007 AS t_mapping 
                ON t_kanban.lv001 = t_mapping.lv004

            -- THAY ĐỔI 1: JOIN bảng Cột (da_lh0005) dựa trên ID trong bảng mapping
            JOIN da_lh0005 AS t_column
                ON t_mapping.lv003 = t_column.lv002
                AND t_column.lv001 = t_kanban.lv018 -- (Optional) Đảm bảo đúng dự án nếu cần

            -- Join Icon đánh giá
            LEFT JOIN da_lh0006 AS eval_icon 
                ON t_kanban.lv017 = eval_icon.lv005 
                AND (eval_icon.lv018 = t_kanban.lv018 OR eval_icon.lv018 = '0')

            -- THAY ĐỔI 2: Join bảng User để lấy tên Assignee
            -- Lưu ý: Logic ON ở đây lặp lại logic COALESCE để tìm đúng người
            LEFT JOIN hr_lv0020 AS assignee_user 
                ON assignee_user.lv001 = COALESCE(
                    (SELECT sa.lv004 
                    FROM da_lh0008 sa 
                    WHERE sa.lv001 = t_master.lv001 
                    AND sa.lv002 = t_mapping.lv003 
                    AND FIND_IN_SET(?, sa.lv003) 
                    LIMIT 1),
                    t_kanban.lv016
                )

            WHERE 
                FIND_IN_SET(?, t_mapping.lv002) > 0 
                AND t_kanban.lv018 = ? 
                
                -- LOGIC 1: Lọc GIAI ĐOẠN (lv003/columnId) đầu tiên chưa hoàn thành
                AND t_mapping.lv003 = ( 
                    SELECT MIN(s1.lv003) 
                    FROM da_lh0007 s1
                    WHERE s1.lv004 = t_kanban.lv001
                    AND ( (LENGTH(s1.lv002) - LENGTH(REPLACE(s1.lv002, ',', '')) + 1) 
                        != 
                        ( SELECT COUNT(DISTINCT tc.lv002)
                            FROM da_lh0009 tc
                            WHERE tc.lv001 = t_master.lv001
                                AND tc.lv004 = 1
                                AND FIND_IN_SET(tc.lv002, s1.lv002) > 0
                        )
                        )
                )
                
                -- LOGIC 2: Lọc ƯU TIÊN (lv009) đầu tiên *TRONG GIAI ĐOẠN ĐÓ* chưa hoàn thành
                AND t_mapping.lv009 = (
                    SELECT MIN(s2.lv009)
                    FROM da_lh0007 s2
                    WHERE s2.lv004 = t_kanban.lv001
                    AND s2.lv003 = t_mapping.lv003 
                    AND ( (LENGTH(s2.lv002) - LENGTH(REPLACE(s2.lv002, ',', '')) + 1) 
                            != 
                            (
                                SELECT COUNT(DISTINCT tc.lv002)
                                FROM da_lh0009 tc
                                WHERE tc.lv001 = t_master.lv001
                                    AND tc.lv004 = 1
                                    AND FIND_IN_SET(tc.lv002, s2.lv002) > 0
                            )
                        )
                )
        ";


        $params = [$departmentId, $departmentId,$departmentId, $projectId]; // 3 tham số ban đầu
        $types = "ssss";

        // ===== ✅ THAY ĐỔI LOGIC LỌC USER =====
        // Nếu user_role KHÔNG phải là 'admin', thì mới lọc theo $userId
        if ($user_role !== 'admin' && $user_role !== 'manager') {
            $active_tasks_query .= " HAVING (assigneeId = ? OR assigneeId IS NULL OR assigneeId = '')";
            $params[] = $userId; // Thêm $userId vào mảng tham số
            $types .= "s";       // Thêm kiểu 's'
        }
        // ======================================

        // Thêm sắp xếp (nếu nhiều task cùng ở 1 bước, sẽ sắp xếp theo lv009)
        $active_tasks_query .= " ORDER BY t_mapping.lv009 ASC";


        $stmt_active = mysqli_prepare($this->db_link, $active_tasks_query);
        mysqli_stmt_bind_param($stmt_active, $types, ...$params);
        mysqli_stmt_execute($stmt_active);
        $active_result = mysqli_stmt_get_result($stmt_active);
        while ($task_row = mysqli_fetch_assoc($active_result)) {
            $tasks[$task_row['id']] = $task_row;
        }
        mysqli_stmt_close($stmt_active);

        // TRUY VẤN 2: Lấy các công việc ĐÃ HOÀN THÀNH
        $completed_tasks_query = "
        SELECT 
                t_kanban.lv001 AS kanbanTaskId, 
                t_master.lv001 AS id, 
                t_kanban.lv004 AS taskId, 
                t_kanban.lv005 AS title, 
                t_kanban.lv013 AS startDate,
                t_kanban.lv019 AS endDate,
                t_kanban.lv007 AS description,
                (
                    SELECT lv003 
                    FROM da_lh0007 
                    WHERE lv018 = ? 
                    AND FIND_IN_SET(?, lv002) > 0 
                    AND lv008 = 1 
                    LIMIT 1
                ) AS columnId,
                COALESCE(
                    (
                        SELECT sa.lv004 
                        FROM da_lh0008 sa
                        WHERE sa.lv001 = t_master.lv001 
                        AND sa.lv003 = t_completion.lv002
                        AND sa.lv002 = (
                            SELECT m.lv003 
                            FROM da_lh0007 m 
                            WHERE m.lv018 = t_kanban.lv018
                                AND FIND_IN_SET(t_completion.lv002, m.lv002) > 0
                                AND m.lv008 = 1
                            LIMIT 1
                        )
                        LIMIT 1
                    ),
                    t_kanban.lv016
                ) AS assigneeId,
                (SELECT ui.lv002 FROM hr_lv0020 ui WHERE ui.lv001 = assigneeId) AS assigneeName,
                1 AS is_completed, 
                t_kanban.lv017 AS evaluation_status,
                eval_icon.lv006 AS evaluation_icon, 
                eval_icon.lv007 AS evaluation_color
            FROM cr_lv0005 AS t_master
            JOIN cr_lv0004 AS t_project 
                ON t_project.lv001 = t_master.lv002 
            JOIN da_lh0003 AS t_kanban 
                ON t_master.lv501 = t_kanban.lv004
            AND t_kanban.lv018 = t_project.lv501 
            JOIN da_lh0009 AS t_completion 
                ON t_master.lv001 = t_completion.lv001
            LEFT JOIN da_lh0006 AS eval_icon 
                ON t_kanban.lv017 = eval_icon.lv005 
            AND (eval_icon.lv018 = t_kanban.lv018 OR eval_icon.lv018 = '0')
            WHERE 
                FIND_IN_SET(?, t_completion.lv002) > 0 
                AND t_kanban.lv018 = ? 
                AND t_completion.lv004 = 1;

        ";

        $stmt_completed = mysqli_prepare($this->db_link, $completed_tasks_query);
        mysqli_stmt_bind_param($stmt_completed, "ssss", $projectId, $departmentId, $departmentId, $projectId);
        mysqli_stmt_execute($stmt_completed);
        $completed_result = mysqli_stmt_get_result($stmt_completed);
        while ($task_row = mysqli_fetch_assoc($completed_result)) {
            $tasks[$task_row['id']] = $task_row;
        }
        mysqli_stmt_close($stmt_completed);

        // TRUY VẤN 3: Lấy các công việc ở trạng thái "ĐANG THỰC HIỆN" (lv008=2)
        // QUAN TRỌNG: Loại trừ các task đã hoàn thành
        $in_progress_tasks_query = "
        SELECT 
                t_kanban.lv001 AS kanbanTaskId, 
                t_master.lv001 AS id, 
                t_kanban.lv004 AS taskId, 
                t_kanban.lv005 AS title, 
                t_kanban.lv007 AS description,
                t_kanban.lv013 AS startDate,
                t_kanban.lv019 AS endDate,
                t_mapping.lv003 AS columnId,
                COALESCE(
                    (SELECT sa.lv004 
                    FROM da_lh0008 sa 
                    WHERE sa.lv001 = t_master.lv001 
                    AND sa.lv002 = t_mapping.lv003 
                    AND FIND_IN_SET(?, sa.lv003) 
                    LIMIT 1),
                    t_kanban.lv016
                ) AS assigneeId,
                (SELECT ui.lv002 FROM hr_lv0020 ui WHERE ui.lv001 = assigneeId) AS assigneeName,
                0 AS is_completed, 
                t_kanban.lv017 AS evaluation_status,
                eval_icon.lv006 AS evaluation_icon, 
                eval_icon.lv007 AS evaluation_color
            FROM cr_lv0005 AS t_master
            JOIN cr_lv0004 AS t_project 
                ON t_project.lv001 = t_master.lv002 
            JOIN da_lh0003 AS t_kanban 
                ON t_master.lv501 = t_kanban.lv004
            AND t_kanban.lv018 = t_project.lv501 
            JOIN da_lh0007 AS t_mapping 
                ON t_kanban.lv001 = t_mapping.lv004
            LEFT JOIN da_lh0006 AS eval_icon 
                ON t_kanban.lv017 = eval_icon.lv005 
            AND (eval_icon.lv018 = t_kanban.lv018 OR eval_icon.lv018 = '0')
            LEFT JOIN da_lh0009 AS t_completion 
                ON t_master.lv001 = t_completion.lv001
                AND FIND_IN_SET(?, t_completion.lv002)
            WHERE 
                FIND_IN_SET(?, t_mapping.lv002) > 0 
                AND t_kanban.lv018 = ? 
                AND t_mapping.lv008 = 2
                AND (t_completion.lv004 IS NULL OR t_completion.lv004 != 1)
        ";

        $in_progress_params = [$departmentId, $departmentId, $departmentId, $projectId];
        $in_progress_types = "ssss";

        // ✅ Áp dụng cùng logic lọc user như truy vấn active tasks - manager và admin xem tất cả
        if ($user_role !== 'admin' && $user_role !== 'manager') {
            $in_progress_tasks_query .= " HAVING (assigneeId = ? OR assigneeId IS NULL OR assigneeId = '')";
            $in_progress_params[] = $userId;
            $in_progress_types .= "s";
        }

        $in_progress_tasks_query .= " ORDER BY t_mapping.lv009 ASC";

        $stmt_in_progress = mysqli_prepare($this->db_link, $in_progress_tasks_query);
        mysqli_stmt_bind_param($stmt_in_progress, $in_progress_types, ...$in_progress_params);
        mysqli_stmt_execute($stmt_in_progress);
        $in_progress_result = mysqli_stmt_get_result($stmt_in_progress);
        while ($task_row = mysqli_fetch_assoc($in_progress_result)) {
            $tasks[$task_row['id']] = $task_row;
        }
        mysqli_stmt_close($stmt_in_progress);

        // Chuyển mảng kết hợp về mảng tuần tự
        $final_tasks = array_values($tasks);

        // --- Phần Gán task vào cột và trả về dữ liệu giữ nguyên ---
        $eval_icons = [];
        $icons_query = "SELECT lv005 as status, lv005 as text, lv006 as icon, lv007 as color FROM da_lh0006 WHERE lv018 = ? OR lv018 = '0'";
        $stmt_icons = mysqli_prepare($this->db_link, $icons_query);
        mysqli_stmt_bind_param($stmt_icons, "s", $projectId);
        mysqli_stmt_execute($stmt_icons);
        $icons_result = mysqli_stmt_get_result($stmt_icons);
        while ($icon_row = mysqli_fetch_assoc($icons_result)) {
            $eval_icons[] = $icon_row;
        }
        mysqli_stmt_close($stmt_icons);

        foreach ($final_tasks as $task) {
            foreach ($columns as &$column) {
                if ($column['id'] == $task['columnId']) {
                    $column['tasks'][] = $task;
                    break;
                }
            }
        }
        unset($column);

        return [
            'users' => $users,
            'columns' => $columns,
            'evaluation_icons' => $eval_icons
        ];
    }

    /**
     * Lấy dữ liệu board của một dự án, hiển thị tất cả các phòng ban liên quan.
     */
    public function get_project_board_all_departments_data($projectId)
    {
        if (empty($projectId)) {
            return ['success' => false, 'message' => 'Thiếu ID Dự án.'];
        }

        $response = [
            'departments' => [],
            'users' => [],
            'evaluation_icons' => []
        ];
        $department_map = [];

        // B1: Lấy toàn bộ dữ liệu liên quan
        $query = "SELECT dept_map.lv002 AS departmentId, dept.lv003 AS departmentName, stage.lv001 AS columnId,
                         stage.lv002 AS columnTitle, dept_map.lv008 as is_done_column, task.lv001 AS id,
                         task.lv004 AS taskId, task.lv005 AS title, task.lv007 AS description,
                         COALESCE(assignee.lv004, task.lv016) AS assigneeId, task.lv017 AS evaluation_status,
                         COALESCE(completion.lv004, 0) as is_completed
                  FROM da_lh0007 AS dept_map
                  JOIN hr_lv0002 AS dept ON dept_map.lv002 = dept.lv001
                  JOIN da_lh0004 AS stage ON dept_map.lv003 = stage.lv001
                  LEFT JOIN da_lh0003 AS task ON dept_map.lv004 = task.lv001 AND dept_map.lv018 = task.lv018
                  LEFT JOIN da_lh0008 AS assignee ON task.lv001 = assignee.lv001 AND dept_map.lv003 = assignee.lv002 AND dept_map.lv002 = assignee.lv003
                  LEFT JOIN da_lh0009 AS completion ON task.lv001 = completion.lv001 AND dept_map.lv002 = completion.lv002
                  WHERE dept_map.lv018 = ?
                  ORDER BY dept.lv003, stage.lv001";

        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $projectId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // B2: Xử lý và sắp xếp dữ liệu
        while ($row = mysqli_fetch_assoc($result)) {
            $deptId = $row['departmentId'];
            $colId = $row['columnId'];

            if (!isset($department_map[$deptId])) {
                $department_map[$deptId] = [
                    'departmentId' => $deptId,
                    'departmentName' => $row['departmentName'],
                    'columns' => []
                ];
            }

            if (!isset($department_map[$deptId]['columns'][$colId])) {
                $department_map[$deptId]['columns'][$colId] = [
                    'id' => $colId,
                    'title' => $row['columnTitle'],
                    'is_done_column' => $row['is_done_column'],
                    'tasks' => []
                ];
            }

            if ($row['id']) {
                $department_map[$deptId]['columns'][$colId]['tasks'][] = [
                    'id' => $row['id'],
                    'taskId' => $row['taskId'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'columnId' => $row['columnId'],
                    'assigneeId' => $row['assigneeId'],
                    'evaluation_status' => $row['evaluation_status'],
                    'is_completed' => (int)$row['is_completed']
                ];
            }
        }
        mysqli_stmt_close($stmt);

        foreach ($department_map as &$dept) {
            $dept['columns'] = array_values($dept['columns']);
        }
        $response['departments'] = array_values($department_map);

        // B3: Lấy dữ liệu phụ (Users và Icons)
        $users_query = "SELECT lv001 as id, lv002 as name FROM hr_lv0020";
        $users_result = db_query($users_query);
        $colors = ['luxury-gradient', 'luxury-gradient-2', 'luxury-gradient-3', 'luxury-gradient-4', 'luxury-gradient-5'];
        $color_index = 0;
        while ($user_row = db_fetch_array($users_result)) {
            $user_row['initials'] = $this->getInitials($user_row['name']);
            $user_row['color'] = $colors[$color_index % count($colors)];
            $response['users'][] = $user_row;
            $color_index++;
        }

        $icons_query = "SELECT lv005 as status, lv005 as text, lv006 as icon, lv007 as color
                        FROM da_lh0006 WHERE lv018 = ? OR lv018 = '0'";
        $stmt_icons = mysqli_prepare($this->db_link, $icons_query);
        mysqli_stmt_bind_param($stmt_icons, "s", $projectId);
        mysqli_stmt_execute($stmt_icons);
        $icons_result = mysqli_stmt_get_result($stmt_icons);
        while ($icon_row = mysqli_fetch_assoc($icons_result)) {
            $response['evaluation_icons'][] = $icon_row;
        }
        mysqli_stmt_close($stmt_icons);

        return $response;
    }
    public function get_user_dashboard_stats_data($userId)
    {
        if (empty($userId)) {
            return [
                'completed_tasks' => 0,
                'inprogress_tasks' => 0,
                'project_count' => 0
            ];
        }

        $stats = [];

        // 1. Đếm số task đã hoàn thành
        $completed_query = "SELECT COUNT(DISTINCT t.lv001) as count
                            FROM da_lh0008 t
                            JOIN da_lh0007 m ON t.lv001 = m.lv004 AND t.lv003 = m.lv002
                            WHERE t.lv004 = ? AND m.lv008 = 1";
        $stmt1 = mysqli_prepare($this->db_link, $completed_query);
        mysqli_stmt_bind_param($stmt1, "s", $userId);
        mysqli_stmt_execute($stmt1);
        $stats['completed_tasks'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt1))['count'] ?? 0;
        mysqli_stmt_close($stmt1);

        // 2. Đếm số task đang làm
        $inprogress_query = "SELECT COUNT(DISTINCT t.lv001) as count
                             FROM da_lh0008 t
                             JOIN da_lh0007 m ON t.lv001 = m.lv004 AND t.lv003 = m.lv002
                             WHERE t.lv004 = ? AND (m.lv008 = 0 OR m.lv008 IS NULL)";
        $stmt2 = mysqli_prepare($this->db_link, $inprogress_query);
        mysqli_stmt_bind_param($stmt2, "s", $userId);
        mysqli_stmt_execute($stmt2);
        $stats['inprogress_tasks'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['count'] ?? 0;
        mysqli_stmt_close($stmt2);

        // 3. Đếm số dự án tham gia
        $projects_query = "SELECT COUNT(DISTINCT m.lv018) as count
                           FROM da_lh0008 t
                           JOIN da_lh0007 m ON t.lv001 = m.lv004 AND t.lv003 = m.lv002
                           WHERE t.lv004 = ?";
        $stmt3 = mysqli_prepare($this->db_link, $projects_query);
        mysqli_stmt_bind_param($stmt3, "s", $userId);
        mysqli_stmt_execute($stmt3);
        $stats['project_count'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt3))['count'] ?? 0;
        mysqli_stmt_close($stmt3);

        return $stats;
    }
    /**
     * Lấy 5 công việc gần đây nhất được giao cho một user cụ thể.
     */
    public function get_recent_tasks_for_user_data($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $tasks = [];

        // ✨ THAY ĐỔI 1: Cập nhật câu lệnh SQL
        // - Thêm LEFT JOIN đến cr_lv0005 để lấy master task ID (lv001).
        // - Thêm một SUBQUERY để kiểm tra sự tồn tại của log trong cr_lv0090.
        $query = "SELECT 
                        t_master.lv001 AS id, 
                        t_kanban.lv005 AS title, 
                        stage.lv002 AS stage_name, 
                        project.lv002 AS projectName,
                        (SELECT 1 FROM cr_lv0090 WHERE lv002 = t_master.lv001 LIMIT 1) as has_history
                  FROM da_lh0003 AS t_kanban
                                    LEFT JOIN cr_lv0005 AS t_master ON t_kanban.lv004 = t_master.lv501
                  JOIN da_lh0008 AS assignee ON t_master.lv001 = assignee.lv001
                  JOIN da_lh0007 AS dept_map ON t_master.lv001 = dept_map.lv004 AND assignee.lv003 = dept_map.lv002 AND assignee.lv002 = dept_map.lv003
                  JOIN da_lh0004 AS stage ON dept_map.lv003 = stage.lv001
                  JOIN cr_lv0004 AS project ON t_kanban.lv018 = project.lv501
                  WHERE assignee.lv004 = ?
                  ORDER BY t_kanban.lv010 DESC
                  LIMIT 100";

        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {

            // ✨ THAY ĐỔI 2: Cập nhật logic xử lý status theo quy tắc mới
            $stage_name_lower = strtolower($row['stage_name']);
            $has_history = !empty($row['has_history']); // Chuyển kết quả subquery thành boolean
            $status = 'todo'; // Mặc định là 'todo'

            if (strpos($stage_name_lower, 'done') !== false || strpos($stage_name_lower, 'hoàn thành') !== false) {
                // Ưu tiên 1: Nếu ở cột DONE -> completed
                $status = 'completed';
            } elseif ($has_history) {
                // Ưu tiên 2: Nếu có lịch sử báo cáo -> in-progress
                $status = 'in-progress';
            }
            // Mặc định: Nếu không thỏa 2 điều kiện trên -> todo

            $tasks[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'status' => $status,
                'projectName' => $row['projectName']
            ];
        }
        mysqli_stmt_close($stmt);

        return $tasks;
    }

    public function get_jo_lv0016_lv007_data($filter = [])
    {
        $query = "SELECT lv017 FROM jo_lv0016";
        $params = [];
        $types = "";

        // Nếu có filter, thêm WHERE
        if (!empty($filter) && is_array($filter)) {
            $where = [];
            foreach ($filter as $key => $val) {
                $where[] = "$key = ?";
                $params[] = $val;
                $types .= "s";
            }
            if (count($where) > 0) {
                $query .= " WHERE " . implode(" AND ", $where);
            }
        }

        $stmt = mysqli_prepare($this->db_link, $query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn.'];
        }

        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn cơ sở dữ liệu.'];
        }

        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row['lv017'];
        }

        mysqli_stmt_close($stmt);
        return ['success' => true, 'data' => $rows];
    }

    public function get_user_task_count($userId, $departmentId)
    {
        $query = "SELECT COUNT(DISTINCT t1.lv001) as task_count 
                  FROM da_lh0008 t1
                  WHERE t1.lv004 = ? AND t1.lv003 = ?
                  AND NOT EXISTS (
                      SELECT 1 FROM da_lh0009 t2 
                      WHERE t2.lv001 = t1.lv001 
                      AND t2.lv006 = ? 
                      AND t2.lv004 = 1
                  )"; // Loại trừ những công việc đã hoàn thành

        $stmt = mysqli_prepare($this->db_link, $query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn đếm công việc.'];
        }

        mysqli_stmt_bind_param($stmt, "sss", $userId, $departmentId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn đếm công việc.'];
        }

        $row = mysqli_fetch_assoc($result);
        $count = (int)$row['task_count'];

        mysqli_stmt_close($stmt);
        return ['success' => true, 'count' => $count];
    }

    public function check_project_plans_data($input)
    {
        // Lấy projectId từ input và kiểm tra
        $projectId = $input['projectId'] ?? null;
        if (empty($projectId)) {
            return ['success' => false, 'message' => 'Thiếu ID dự án để kiểm tra.'];
        }

        $query = "SELECT lv001 as id , lv002 as planName FROM cr_lv0004 WHERE lv501 = ?";

        // Sử dụng prepared statement để chống SQL Injection
        $stmt = mysqli_prepare($this->db_link, $query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn: ' . mysqli_error($this->db_link)];
        }

        mysqli_stmt_bind_param($stmt, "s", $projectId);

        // Thực thi câu lệnh
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $plans = [];
            // Lấy tất cả các dòng kết quả
            while ($row = mysqli_fetch_assoc($result)) {
                $plans[] = $row;
            }

            // Trả về thành công cùng với dữ liệu (có thể là mảng rỗng)
            return ['success' => true, 'data' => $plans];
        } else {
            // Trả về lỗi nếu thực thi thất bại
            return ['success' => false, 'message' => 'Lỗi khi thực thi truy vấn: ' . mysqli_stmt_error($stmt)];
        }
    }
    // ===================================================================
    // ==                     CÁC HÀM TẠO MỚI (CREATE)                   ==
    // ===================================================================
    // haolam.php
    public function get_child_projects($parentId)
    {
        $query = "SELECT lv001 as id, lv002 as name, lv003 as status, lv004 as description, lv005 as createdBy, lv006 as createdAt FROM da_lh0002 WHERE parent_id = ?";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $parentId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $projects = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $projects[] = $row;
        }
        mysqli_stmt_close($stmt);

        return ['success' => true, 'data' => $projects];
    }
    public function clone_project($templateProjectId, $userId)
    {
        // 1. Clone dự án (da_lh0002)
        $query = "SELECT lv002, lv004 FROM da_lh0002 WHERE lv001 = ?";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $templateProjectId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $project = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$project) return false;

        $projectNameClone = $project['lv002'] . "_CLONE_" . time();
        $descriptionClone = $project['lv004'];

        // Khi insert dự án clone
        $insertQuery = "INSERT INTO da_lh0002 (lv002, lv003, lv004, lv005, lv006, parent_id) VALUES (?, '1', ?, ?, NOW(), ?)";
        $insertStmt = mysqli_prepare($this->db_link, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ssss", $projectNameClone, $descriptionClone, $userId, $templateProjectId);
        mysqli_stmt_execute($insertStmt);
        $newProjectId = mysqli_insert_id($this->db_link);
        mysqli_stmt_close($insertStmt);

        // 2. Clone phases (da_lh0004 + da_lh0005)
        $phaseQuery = "SELECT t2.lv001, t2.lv002 FROM da_lh0005 AS t1 JOIN da_lh0004 AS t2 ON t1.lv002 = t2.lv001 WHERE t1.lv001 = ?";
        $stmtPhase = mysqli_prepare($this->db_link, $phaseQuery);
        mysqli_stmt_bind_param($stmtPhase, "s", $templateProjectId);
        mysqli_stmt_execute($stmtPhase);
        $resultPhase = mysqli_stmt_get_result($stmtPhase);
        $phases = [];
        while ($row = mysqli_fetch_assoc($resultPhase)) {
            $phases[] = $row;
        }
        mysqli_stmt_close($stmtPhase);
        $this->addAllDepartmentsToProject($newProjectId);

        foreach ($phases as $phase) {
            // Insert vào da_lh0004 nếu muốn clone phase thật sự, hoặc chỉ mapping vào da_lh0005 nếu dùng chung định nghĩa phase
            $insertPhaseMap = "INSERT INTO da_lh0005 (lv001, lv002, lv003) VALUES (?, ?, 1)";
            $stmtInsertPhase = mysqli_prepare($this->db_link, $insertPhaseMap);
            mysqli_stmt_bind_param($stmtInsertPhase, "ss", $newProjectId, $phase['lv001']);
            mysqli_stmt_execute($stmtInsertPhase);
            mysqli_stmt_close($stmtInsertPhase);
        }

        // 3. Clone tasks (da_lh0003)
        $taskQuery = "SELECT lv003, lv004, lv005, lv006, lv009, lv014, lv012, lv013, lv019 FROM da_lh0003 WHERE lv018 = ?";
        $stmtTask = mysqli_prepare($this->db_link, $taskQuery);
        mysqli_stmt_bind_param($stmtTask, "s", $templateProjectId);
        mysqli_stmt_execute($stmtTask);
        $resultTask = mysqli_stmt_get_result($stmtTask);

        while ($task = mysqli_fetch_assoc($resultTask)) {
            $newTaskCode = 'TASK-' . strtoupper(substr(md5(time()), 0, 4)) . rand(100, 999);
            $insertTask = "INSERT INTO da_lh0003 (lv003, lv004, lv005, lv006, lv009, lv014, lv010, lv012, lv013, lv019, lv018) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
            $stmtInsertTask = mysqli_prepare($this->db_link, $insertTask);
            mysqli_stmt_bind_param(
                $stmtInsertTask,
                "ssssssisss",
                $task['lv003'],
                $newTaskCode,
                $task['lv005'],
                $task['lv006'],
                $task['lv009'],
                $task['lv014'],
                $task['lv012'],
                $task['lv013'],
                $task['lv019'],
                $newProjectId
            );
            mysqli_stmt_execute($stmtInsertTask);
            mysqli_stmt_close($stmtInsertTask);
        }
        mysqli_stmt_close($stmtTask);

        return $newProjectId;
    }
    function IncrementCode($code)
    {
        if (preg_match('/(.*?)([0-9]+)$/', $code, $matches)) {
            $prefix = $matches[1];
            $numberStr = $matches[2];
            $length = strlen($numberStr);
            $newNumber = intval($numberStr) + 1;
            return $prefix . str_pad($newNumber, $length, '0', STR_PAD_LEFT);
        }
        // Nếu mã không có số ở cuối, trả về mã cũ và thêm số 1
        return $code . '1';
    }
    /**
     * Tạo một công việc mới.
     */
    public function create_task_data($data, $userId)
    {
        $title = isset($data['title']) ? trim($data['title']) : '';
        $description = isset($data['description']) ? trim($data['description']) : '';
        $columnId = isset($data['columnId']) ? (int)$data['columnId'] : 0;
        $assigneeId = isset($data['assigneeId']) ? $data['assigneeId'] : null;
        $priority = isset($data['priority']) ? (int)$data['priority'] : 2;
        $startDate = isset($data['startDate']) && !empty($data['startDate']) ? $data['startDate'] : null;
        $endDate = isset($data['endDate']) && !empty($data['endDate']) ? $data['endDate'] : null;
        $projectId = isset($data['projectId']) ? $data['projectId'] : '0';
        $planId = isset($data['planId']) ? trim($data['planId']) : '';


        if (empty($title) || $columnId <= 0 || empty($projectId) || empty($planId)) {
            return ['success' => false, 'message' => 'Thông tin công việc, giai đoạn, dự án và kế hoạch là bắt buộc.'];
        }

        mysqli_autocommit($this->db_link, false);
        $success = true;

        // BƯỚC A: INSERT VÀO BẢNG KANBAN (da_lh0003)
        $taskIdStr = 'TASK-' . strtoupper(substr(md5(time()), 0, 4));
        $createdDate = date('Y-m-d H:i:s');
        $status = 1;
        $empty = '';
        $query1 = "INSERT INTO da_lh0003 (lv003, lv004, lv005, lv006, lv009, lv014, lv010, lv011, lv012, lv013, lv019, lv018) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt1 = mysqli_prepare($this->db_link, $query1);
        mysqli_stmt_bind_param($stmt1, "issssssiisss", $columnId, $taskIdStr, $title, $description, $userId, $assigneeId, $createdDate, $status, $priority, $startDate, $endDate, $projectId);
        if (!mysqli_stmt_execute($stmt1)) {
            $success = false;
        }
        mysqli_stmt_close($stmt1);

        // ----- BƯỚC B: INSERT VÀO BẢNG GỐC (cr_lv0005) -----
        if ($success) {
            $taskTypeCode = $taskIdStr;
            $taskNameInMaster = $description;
            $completionDate = $endDate;
            $masterStatus = 0;

            $query2 = "INSERT INTO cr_lv0005 (lv002, lv501, lv003, lv004, lv005, lv009, lv010, lv011,lv006) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)";
            $stmt2 = mysqli_prepare($this->db_link, $query2);
            mysqli_stmt_bind_param($stmt2, "sssssssis", $planId, $taskIdStr, $taskTypeCode, $taskNameInMaster, $completionDate, $userId, $createdDate, $masterStatus, $empty);
            $success = mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
        }

        // ----- BƯỚC C: INSERT VÀO BẢNG LOGIC (cr_lv0003) -----
        if ($success) {
            $next_task_id = $taskIdStr;
            while (true) {
                $safe_next_task_id = sof_escape_string($next_task_id);
                $check_sql = "SELECT lv001 FROM cr_lv0003 WHERE lv001 = '$safe_next_task_id'";
                $check_result = db_query($check_sql);
                if (db_num_rows($check_result) == 0) {
                    break;
                } else {
                    $next_task_id = IncrementCode($next_task_id);
                }
            }
            $final_task_id_for_cr0003 = sof_escape_string($next_task_id);

            $sql_max_order = "SELECT MAX(lv008) AS max_order FROM cr_lv0003 WHERE lv099 = 'TASK'";
            $row_max = db_fetch_array(db_query($sql_max_order));
            $new_order = ($row_max['max_order'] ? (int)$row_max['max_order'] : 0) + 1;

            $val_lv003 = 0;
            $val_lv099 = 'TASK';
            $val_lv009 = 1;

            // C3. Thực hiện INSERT vào cr_lv0003
            $query3 = "INSERT INTO cr_lv0003 (lv001, lv002, lv004, lv003, lv099, lv009, lv005, lv006, lv007, lv008) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt3 = mysqli_prepare($this->db_link, $query3);
            mysqli_stmt_bind_param(
                $stmt3,
                "sssisissis",
                $final_task_id_for_cr0003,
                $title,
                $description,
                $val_lv003,
                $val_lv099,
                $val_lv009,
                $userId,
                $createdDate,
                $description,
                $new_order
            );

            $success = mysqli_stmt_execute($stmt3);
            mysqli_stmt_close($stmt3);
        }

        // Commit transaction và trả về kết quả
        if ($success) {
            mysqli_commit($this->db_link);
            mysqli_autocommit($this->db_link, true);
            return ['success' => true];
        } else {
            mysqli_rollback($this->db_link);
            mysqli_autocommit($this->db_link, true);
            return ['success' => false, 'message' => 'Lỗi khi ghi vào cơ sở dữ liệu.'];
        }
    }

    // Lấy danh sách tất cả giai đoạn
    public function get_phases_data($data = [])
    {
        $query = "SELECT lv001 as id, lv002 as name, lv006 as createdAt, lv005 as createdBy 
              FROM da_lh0004 
              WHERE lv003 = '0' 
              ORDER BY lv006 DESC";

        $result = mysqli_query($this->db_link, $query);

        if (!$result) {
            return ['success' => false, 'message' => 'Lỗi khi truy vấn cơ sở dữ liệu.'];
        }

        $phases = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Mặc định người tạo là SOF001 (Admin)
            $row['createdBy'] = 'SOF001';
            $phases[] = $row;
        }

        mysqli_free_result($result);
        return ['success' => true, 'data' => $phases];
    }

    // Cập nhật giai đoạn
    public function update_column_data($data)
    {
        $id = isset($data['id']) ? trim($data['id']) : '';
        $columnName = isset($data['name']) ? trim($data['name']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($id)) {
            return ['success' => false, 'message' => 'ID giai đoạn không được để trống.'];
        }

        if (empty($columnName)) {
            return ['success' => false, 'message' => 'Tên giai đoạn không được để trống.'];
        }

        // Kiểm tra giai đoạn có tồn tại không
        $checkQuery = "SELECT lv001 FROM da_lh0004 WHERE lv001 = ? AND lv003 = '0'";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra giai đoạn.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Giai đoạn không tồn tại.'];
        }
        mysqli_stmt_close($checkStmt);

        // Kiểm tra tên trùng lặp
        $duplicateQuery = "SELECT lv001 FROM da_lh0004 WHERE lv002 = ? AND lv001 != ? AND lv003 = '0'";
        $duplicateStmt = mysqli_prepare($this->db_link, $duplicateQuery);

        if (!$duplicateStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra tên trùng lặp.'];
        }

        mysqli_stmt_bind_param($duplicateStmt, "ss", $columnName, $id);
        mysqli_stmt_execute($duplicateStmt);
        $duplicateResult = mysqli_stmt_get_result($duplicateStmt);

        if (mysqli_num_rows($duplicateResult) > 0) {
            mysqli_stmt_close($duplicateStmt);
            return ['success' => false, 'message' => 'Tên giai đoạn đã tồn tại.'];
        }
        mysqli_stmt_close($duplicateStmt);

        // Cập nhật giai đoạn
        $updateQuery = "UPDATE da_lh0004 SET lv002 = ?, lv006 = NOW(), lv005 = ? WHERE lv001 = ? AND lv003 = '0'";
        $updateStmt = mysqli_prepare($this->db_link, $updateQuery);

        if (!$updateStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh cập nhật.'];
        }

        mysqli_stmt_bind_param($updateStmt, "sss", $columnName, $userId, $id);

        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            return ['success' => true, 'message' => 'Cập nhật giai đoạn thành công.'];
        } else {
            $error = mysqli_stmt_error($updateStmt);
            mysqli_stmt_close($updateStmt);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật giai đoạn.', 'error' => $error];
        }
    }

    // Xóa giai đoạn
    public function delete_column_data($data)
    {
        $id = isset($data['id']) ? trim($data['id']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($id)) {
            return ['success' => false, 'message' => 'ID giai đoạn không được để trống.'];
        }

        // Kiểm tra giai đoạn có tồn tại không
        $checkQuery = "SELECT lv001, lv002 FROM da_lh0004 WHERE lv001 = ? AND lv003 = '0'";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra giai đoạn.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Giai đoạn không tồn tại.'];
        }

        $phaseData = mysqli_fetch_assoc($checkResult);
        mysqli_stmt_close($checkStmt);

        // Kiểm tra xem giai đoạn có đang được sử dụng trong các task không
        $taskCheckQuery = "SELECT COUNT(*) as task_count FROM da_lh0003 WHERE lv003 = ?";
        $taskCheckStmt = mysqli_prepare($this->db_link, $taskCheckQuery);

        if ($taskCheckStmt) {
            mysqli_stmt_bind_param($taskCheckStmt, "s", $id);
            mysqli_stmt_execute($taskCheckStmt);
            $taskCheckResult = mysqli_stmt_get_result($taskCheckStmt);
            $taskCount = mysqli_fetch_assoc($taskCheckResult);

            if ($taskCount && $taskCount['task_count'] > 0) {
                mysqli_stmt_close($taskCheckStmt);
                return ['success' => false, 'message' => 'Không thể xóa giai đoạn này vì đang có ' . $taskCount['task_count'] . ' task sử dụng.'];
            }
            mysqli_stmt_close($taskCheckStmt);
        }

        // Xóa giai đoạn (soft delete bằng cách cập nhật lv003 = '1')
        $deleteQuery = "UPDATE da_lh0004 SET lv003 = '1', lv006 = NOW(), lv005 = ? WHERE lv001 = ? AND lv003 = '0'";
        $deleteStmt = mysqli_prepare($this->db_link, $deleteQuery);

        if (!$deleteStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh xóa.'];
        }

        mysqli_stmt_bind_param($deleteStmt, "ss", $userId, $id);

        if (mysqli_stmt_execute($deleteStmt)) {
            $affectedRows = mysqli_stmt_affected_rows($deleteStmt);
            mysqli_stmt_close($deleteStmt);

            if ($affectedRows > 0) {
                return ['success' => true, 'message' => 'Xóa giai đoạn "' . $phaseData['lv002'] . '" thành công.'];
            } else {
                return ['success' => false, 'message' => 'Không thể xóa giai đoạn.'];
            }
        } else {
            $error = mysqli_stmt_error($deleteStmt);
            mysqli_stmt_close($deleteStmt);
            return ['success' => false, 'message' => 'Lỗi khi xóa giai đoạn.', 'error' => $error];
        }
    }
    // === TASK MANAGEMENT FUNCTIONS ===

    // Lấy tất cả công việc của một dự án
    public function get_tasks_by_project_data($data)
    {
        $projectId = isset($data['projectId']) ? trim($data['projectId']) : '';

        if (empty($projectId)) {
            return ['success' => false, 'message' => 'ID dự án không được để trống.'];
        }

        $tasks_query = "SELECT 
                            COALESCE(t_master.lv001, t_kanban.lv001) as id, -- Ưu tiên master, nếu NULL thì lấy kanban
                            t_kanban.lv001 as kanban_task_id,
                            t_kanban.lv002 as taskCode,
                            t_kanban.lv003 as phaseId,
                            t_kanban.lv004 as projectCode,
                            t_kanban.lv005 as taskName,
                            t_kanban.lv006 as description,
                            t_kanban.lv017 as evaluation_status,
                            t_kanban.lv009 as createdBy,
                            t_kanban.lv010 as createdAt,
                            t_kanban.lv012 as priority,
                            t_kanban.lv013 as startDate,
                            t_kanban.lv018 as projectId,
                            t_kanban.lv019 as completedDate
                        FROM da_lh0003 AS t_kanban
                        LEFT JOIN cr_lv0005 AS t_master 
                            ON t_kanban.lv004 = t_master.lv501
                        WHERE t_kanban.lv018 = ?;
                        ";
        $stmt = mysqli_prepare($this->db_link, $tasks_query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn.'];
        }

        mysqli_stmt_bind_param($stmt, "s", $projectId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn cơ sở dữ liệu.'];
        }

        $tasks = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Chuyển đổi kiểu dữ liệu
            $row['status'] = (int)$row['status'];
            $row['priority'] = (int)$row['priority'];
            $row['estimatedHours'] = (int)$row['estimatedHours'];

            $tasks[] = $row;
        }

        mysqli_stmt_close($stmt);
        return ['success' => true, 'data' => $tasks];
    }

    // Tạo công việc mới
    public function create_taskda3_data($data)
    {
        $projectId = isset($data['projectId']) ? trim($data['projectId']) : '';
        $taskName = isset($data['taskName']) ? trim($data['taskName']) : '';
        $description = isset($data['description']) ? trim($data['description']) : '';
        $phaseId = isset($data['phaseId']) ? trim($data['phaseId']) : '';
        $priority = isset($data['priority']) ? (int)$data['priority'] : 1;
        $startDate = isset($data['startDate']) ? trim($data['startDate']) : '';
        $endDate = isset($data['endDate']) ? trim($data['endDate']) : '';
        $assignee = isset($data['assignee']) ? trim($data['assignee']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($projectId)) {
            return ['success' => false, 'message' => 'ID dự án không được để trống.'];
        }

        if (empty($taskName)) {
            return ['success' => false, 'message' => 'Tên công việc không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // Kiểm tra dự án có tồn tại không
        $checkProjectQuery = "SELECT lv001 FROM da_lh0002 WHERE lv001 = ?";
        $checkProjectStmt = mysqli_prepare($this->db_link, $checkProjectQuery);

        if (!$checkProjectStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra dự án.'];
        }

        mysqli_stmt_bind_param($checkProjectStmt, "s", $projectId);
        mysqli_stmt_execute($checkProjectStmt);
        $checkProjectResult = mysqli_stmt_get_result($checkProjectStmt);

        if (mysqli_num_rows($checkProjectResult) == 0) {
            mysqli_stmt_close($checkProjectStmt);
            return ['success' => false, 'message' => 'Dự án không tồn tại.'];
        }
        mysqli_stmt_close($checkProjectStmt);

        // Tạo ID và mã code cho task
        $taskCode =  'TASK-' . strtoupper(substr(md5(time()), 0, 4));

        // Thêm task mới
        $insertQuery = "INSERT INTO da_lh0003 (lv004, lv003, lv018, lv005, lv006, 
                    lv009, lv010, lv011, lv012, lv013, lv019) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), 1, ?, ?, ?)";

        $insertStmt = mysqli_prepare($this->db_link, $insertQuery);

        if (!$insertStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh thêm công việc.'];
        }

        mysqli_stmt_bind_param(
            $insertStmt,
            "ssssssiss",
            $taskCode,
            $phaseId,
            $projectId,
            $taskName,
            $description,
            $userId,
            $priority,
            $startDate,
            $endDate
        );

        if (mysqli_stmt_execute($insertStmt)) {
            mysqli_stmt_close($insertStmt);
            return [
                'success' => true,
                'message' => 'Tạo công việc thành công.',
                'data' => [
                    'id' => $taskId,
                    'taskCode' => $taskCode,
                    'taskName' => $taskName,
                    'projectId' => $projectId,
                    'createdAt' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            $error = mysqli_stmt_error($insertStmt);
            mysqli_stmt_close($insertStmt);
            return ['success' => false, 'message' => 'Lỗi khi tạo công việc.', 'error' => $error];
        }
    }

    // Cập nhật công việc
    public function update_task_data($data)
    {
        $taskId = isset($data['id']) ? trim($data['id']) : '';
        $taskName = isset($data['taskName']) ? trim($data['taskName']) : '';
        $description = isset($data['description']) ? trim($data['description']) : '';
        $phaseId = isset($data['phaseId']) ? trim($data['phaseId']) : '';
        $priority = isset($data['priority']) ? (int)$data['priority'] : 1;
        $status = isset($data['status']) ? (int)$data['status'] : 1;
        $startDate = isset($data['startDate']) ? trim($data['startDate']) : '';
        $endDate = isset($data['endDate']) ? trim($data['endDate']) : '';
        $assignee = isset($data['assignee']) ? trim($data['assignee']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($taskId)) {
            return ['success' => false, 'message' => 'ID công việc không được để trống.'];
        }

        if (empty($taskName)) {
            return ['success' => false, 'message' => 'Tên công việc không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // // Kiểm tra task có tồn tại không
        // $checkQuery = "SELECT lv001 FROM cr_lv0005 WHERE lv001 = ?";
        // $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        // if (!$checkStmt) {
        //     return ['success' => false, 'message' => 'Lỗi khi kiểm tra công việc.'];
        // }

        // mysqli_stmt_bind_param($checkStmt, "s", $taskId);
        // mysqli_stmt_execute($checkStmt);
        // $checkResult = mysqli_stmt_get_result($checkStmt);

        // if (mysqli_num_rows($checkResult) == 0) {
        //     mysqli_stmt_close($checkStmt);
        //     return ['success' => false, 'message' => 'Công việc không tồn tại.'];
        // }
        // mysqli_stmt_close($checkStmt);

        // Cập nhật task
        $updateQuery = "UPDATE da_lh0003 AS t_kanban
                        LEFT JOIN cr_lv0005 AS t_master 
                            ON t_master.lv501 = t_kanban.lv004
                        SET 
                            t_kanban.lv005 = ?,
                            t_kanban.lv006 = ?,
                            t_kanban.lv003 = ?,
                            t_kanban.lv011 = ?,
                            t_kanban.lv012 = ?,
                            t_kanban.lv013 = ?,
                            t_kanban.lv019 = ?,
                            t_kanban.lv010 = NOW()
                        WHERE t_master.lv001 = ? OR t_master.lv001 IS NULL;
                        ";

        $updateStmt = mysqli_prepare($this->db_link, $updateQuery);

        if (!$updateStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh cập nhật.'];
        }

        mysqli_stmt_bind_param(
            $updateStmt,
            "sssiisss",
            $taskName,
            $description,
            $phaseId,
            $status,
            $priority,
            $startDate,
            $endDate,
            $taskId
        );

        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            return ['success' => true, 'message' => 'Cập nhật công việc thành công.'];
        } else {
            $error = mysqli_stmt_error($updateStmt);
            mysqli_stmt_close($updateStmt);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật công việc.', 'error' => $error];
        }
    }

    // Xóa công việc
    public function delete_task_data($data)
    {
        $taskId = isset($data['id']) ? trim($data['id']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($taskId)) {
            return ['success' => false, 'message' => 'ID công việc không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // Kiểm tra task có tồn tại trong cr_lv0005 không
        $checkQuery = "SELECT lv001, lv005, lv501 FROM cr_lv0005 WHERE lv001 = ?";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra công việc.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $taskId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        $taskData = null;
        if (mysqli_num_rows($checkResult) > 0) {
            $taskData = mysqli_fetch_assoc($checkResult);
        }
        mysqli_stmt_close($checkStmt);

        // Bắt đầu transaction
        mysqli_autocommit($this->db_link, false);

        try {
            // Nếu có trong cr_lv0005 thì xóa ở da_lh0003 bằng lv004 = lv501
            if ($taskData && !empty($taskData['lv501'])) {
                $deleteKanbanQuery = "DELETE FROM da_lh0003 WHERE lv004 = ?";
                $deleteKanbanStmt = mysqli_prepare($this->db_link, $deleteKanbanQuery);

                if (!$deleteKanbanStmt) {
                    throw new Exception('Lỗi khi chuẩn bị câu lệnh xóa dữ liệu kanban.');
                }

                mysqli_stmt_bind_param($deleteKanbanStmt, "s", $taskData['lv501']);

                if (!mysqli_stmt_execute($deleteKanbanStmt)) {
                    $err = mysqli_stmt_error($deleteKanbanStmt);
                    mysqli_stmt_close($deleteKanbanStmt);
                    throw new Exception('Lỗi khi xóa dữ liệu kanban: ' . $err);
                }

                mysqli_stmt_close($deleteKanbanStmt);

                // Xóa task chính trong cr_lv0005
                $deleteTaskQuery = "DELETE FROM cr_lv0005 WHERE lv001 = ?";
                $deleteTaskStmt = mysqli_prepare($this->db_link, $deleteTaskQuery);

                if (!$deleteTaskStmt) {
                    throw new Exception('Lỗi khi chuẩn bị câu lệnh xóa công việc.');
                }

                mysqli_stmt_bind_param($deleteTaskStmt, "s", $taskId);

                if (!mysqli_stmt_execute($deleteTaskStmt)) {
                    $err = mysqli_stmt_error($deleteTaskStmt);
                    mysqli_stmt_close($deleteTaskStmt);
                    throw new Exception('Lỗi khi xóa công việc: ' . $err);
                }

                $affectedRows = mysqli_stmt_affected_rows($deleteTaskStmt);
                mysqli_stmt_close($deleteTaskStmt);

                if ($affectedRows == 0) {
                    throw new Exception('Không thể xóa công việc.');
                }

                $msg = 'Xóa công việc "' . $taskData['lv005'] . '" và dữ liệu liên quan thành công.';
            } else {
                // Nếu không có trong cr_lv0005 thì chỉ xóa trong da_lh0003 theo lv001
                $deleteKanbanQuery = "DELETE FROM da_lh0003 WHERE lv001 = ?";
                $deleteKanbanStmt = mysqli_prepare($this->db_link, $deleteKanbanQuery);

                if (!$deleteKanbanStmt) {
                    throw new Exception('Lỗi khi chuẩn bị câu lệnh xóa công việc kanban.');
                }

                mysqli_stmt_bind_param($deleteKanbanStmt, "s", $taskId);

                if (!mysqli_stmt_execute($deleteKanbanStmt)) {
                    $err = mysqli_stmt_error($deleteKanbanStmt);
                    mysqli_stmt_close($deleteKanbanStmt);
                    throw new Exception('Lỗi khi xóa công việc kanban: ' . $err);
                }

                $affectedRows = mysqli_stmt_affected_rows($deleteKanbanStmt);
                mysqli_stmt_close($deleteKanbanStmt);

                if ($affectedRows == 0) {
                    throw new Exception('Không tìm thấy công việc để xóa trong da_lh0003.');
                }

                $msg = 'Xóa công việc trong bảng da_lh0003 thành công.';
            }

            // Commit transaction
            mysqli_commit($this->db_link);
            mysqli_autocommit($this->db_link, true);

            return ['success' => true, 'message' => $msg];
        } catch (Exception $e) {
            // Rollback transaction khi có lỗi
            mysqli_rollback($this->db_link);
            mysqli_autocommit($this->db_link, true);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // === DEPARTMENT AND TASK ASSIGNMENT FUNCTIONS ===

    // Lấy danh sách tất cả phòng ban
    public function get_departments_data($data = [])
    {
        $query = "SELECT lv001 as id, lv003 as name 
              FROM hr_lv0002 
              WHERE lv003 IS NOT NULL AND lv003 != ''
              ORDER BY lv003 ASC";

        $result = mysqli_query($this->db_link, $query);

        if (!$result) {
            return ['success' => false, 'message' => 'Lỗi khi truy vấn danh sách phòng ban.'];
        }

        $departments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $departments[] = $row;
        }

        mysqli_free_result($result);
        return ['success' => true, 'data' => $departments];
    }
    public function addAllDepartmentsToProject($projectId)
    {
        // Lấy danh sách phòng ban
        $departmentsResult = $this->get_departments_data();
        if (!$departmentsResult['success']) {
            return ['success' => false, 'message' => 'Không lấy được phòng ban'];
        }
        $departments = $departmentsResult['data'];
        if (empty($departments)) {
            return ['success' => false, 'message' => 'Không có phòng ban'];
        }

        // Gộp tất cả mã phòng ban thành chuỗi "pb1,pb2,pb3"
        $departmentIds = array_column($departments, 'id');
        $lv002 = implode(',', $departmentIds);

        // Insert một dòng vào da_lh0007
        $query = "INSERT INTO da_lh0007 (lv018, lv002,lv003, lv008, lv004) VALUES (?, ?,7, 1, 0)";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "is", $projectId, $lv002);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return [
                'success' => true,
                'message' => 'Đã thêm phòng ban vào dự án (gộp một dòng)',
                'lv002' => $lv002
            ];
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi thêm phòng ban', 'error' => $error];
        }
    }

    // Lấy danh sách phân công của một task (đã cập nhật để xử lý comma-separated departments)
    public function get_task_assignments_data($data)
    {
        $taskId = isset($data['taskId']) ? trim($data['taskId']) : '';

        if (empty($taskId)) {
            return ['success' => false, 'message' => 'ID công việc không được để trống.'];
        }

        $query = "SELECT 
                ta.lv001 as assignmentId,
                ta.lv018 as projectId,
                ta.lv002 as departmentIds,
                ta.lv003 as phaseId,
                ta.lv004 as taskId,
                ta.lv005 as processStep,
                ta.lv008 as isDone,
                ta.lv009 as priority
              FROM da_lh0007 ta
              WHERE ta.lv004 = ?";

        $stmt = mysqli_prepare($this->db_link, $query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn phân công.'];
        }

        mysqli_stmt_bind_param($stmt, "s", $taskId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn phân công.'];
        }

        $assignments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Chuyển đổi kiểu dữ liệu
            $row['isDone'] = (int)$row['isDone'];
            $row['priority'] = (int)$row['priority'];
            $row['processStep'] = (int)$row['processStep'];

            // Tách departmentIds và lấy tên phòng ban
            $departmentIds = explode(',', $row['departmentIds']);
            $departmentNames = [];

            if (!empty($departmentIds) && $departmentIds[0] !== '') {
                $placeholders = str_repeat('?,', count($departmentIds) - 1) . '?';
                $deptQuery = "SELECT lv001, lv003 as name FROM hr_lv0002 WHERE lv001 IN ($placeholders)";
                $deptStmt = mysqli_prepare($this->db_link, $deptQuery);

                if ($deptStmt) {
                    mysqli_stmt_bind_param($deptStmt, str_repeat('s', count($departmentIds)), ...$departmentIds);
                    mysqli_stmt_execute($deptStmt);
                    $deptResult = mysqli_stmt_get_result($deptStmt);

                    while ($deptRow = mysqli_fetch_assoc($deptResult)) {
                        $departmentNames[] = $deptRow['name'];
                    }
                    mysqli_stmt_close($deptStmt);
                }
            }

            $row['departmentNames'] = implode(', ', $departmentNames);
            $row['departmentIdArray'] = $departmentIds;

            $assignments[] = $row;
        }

        mysqli_stmt_close($stmt);
        return ['success' => true, 'data' => $assignments];
    }

    // Phân công task cho phòng ban
    public function assign_task_to_departments_data($data)
    {
        $taskId = isset($data['taskId']) ? trim($data['taskId']) : '';
        $departmentIds = isset($data['departmentIds']) ? $data['departmentIds'] : []; // Array of department IDs
        $priority = isset($data['priority']) ? (int)$data['priority'] : 1;
        $projectId = isset($data['projectId']) ? trim($data['projectId']) : '';
        $phaseId = isset($data['phaseId']) ? trim($data['phaseId']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';
        $processStep = isset($data['processStep']) ? (int)$data['processStep'] : 1; // Bước quy trình

        if (empty($taskId)) {
            return ['success' => false, 'message' => 'ID công việc không được để trống.'];
        }

        if (empty($departmentIds) || !is_array($departmentIds)) {
            return ['success' => false, 'message' => 'Danh sách phòng ban không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // // Kiểm tra task có tồn tại không
        // $checkTaskQuery = "SELECT lv001, lv004 as projectId, lv003 as phaseId FROM da_lh0003 WHERE lv001 = ?";
        // $checkTaskStmt = mysqli_prepare($this->db_link, $checkTaskQuery);

        // if (!$checkTaskStmt) {
        //     return ['success' => false, 'message' => 'Lỗi khi kiểm tra công việc.'];
        // }

        // mysqli_stmt_bind_param($checkTaskStmt, "s", $taskId);
        // mysqli_stmt_execute($checkTaskStmt);
        // $checkTaskResult = mysqli_stmt_get_result($checkTaskStmt);

        // if (mysqli_num_rows($checkTaskResult) == 0) {
        //     mysqli_stmt_close($checkTaskStmt);
        //     return ['success' => false, 'message' => 'Công việc không tồn tại.'];
        // }

        // $taskData = mysqli_fetch_assoc($checkTaskResult);
        // mysqli_stmt_close($checkTaskStmt);

        // Sử dụng projectId và phaseId từ task nếu không được cung cấp
        if (empty($projectId)) {
            $projectId = $taskData['projectId'];
        }
        if (empty($phaseId)) {
            $phaseId = $taskData['phaseId'];
        }

        // Validate tất cả department IDs
        if (!empty($departmentIds)) {
            $placeholders = str_repeat('?,', count($departmentIds) - 1) . '?';
            $checkDeptQuery = "SELECT COUNT(*) as valid_count FROM hr_lv0002 WHERE lv001 IN ($placeholders)";
            $checkDeptStmt = mysqli_prepare($this->db_link, $checkDeptQuery);

            if (!$checkDeptStmt) {
                return ['success' => false, 'message' => 'Lỗi khi kiểm tra phòng ban.'];
            }

            mysqli_stmt_bind_param($checkDeptStmt, str_repeat('s', count($departmentIds)), ...$departmentIds);
            mysqli_stmt_execute($checkDeptStmt);
            $checkDeptResult = mysqli_stmt_get_result($checkDeptStmt);
            $validCount = mysqli_fetch_assoc($checkDeptResult);

            if ($validCount['valid_count'] != count($departmentIds)) {
                mysqli_stmt_close($checkDeptStmt);
                return ['success' => false, 'message' => 'Một hoặc nhiều phòng ban không tồn tại.'];
            }
            mysqli_stmt_close($checkDeptStmt);
        }

        // Mỗi lần phân công là một dòng mới, không cập nhật dòng cũ
        // Chuẩn bị dữ liệu để insert
        $departmentIdsString = implode(',', $departmentIds);

        $insertQuery = "INSERT INTO da_lh0007 (lv002, lv003, lv004, lv005, lv008, lv009, lv018) 
                VALUES (?, ?, ?, ?, 0, ?, ?)";

        $insertStmt = mysqli_prepare($this->db_link, $insertQuery);

        if (!$insertStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh phân công.'];
        }

        // lv002: departmentIdsString, lv003: phaseId, lv004: taskId, lv005: processStep, lv008: 0, lv009: priority, lv018: projectId
        mysqli_stmt_bind_param(
            $insertStmt,
            "sisiii",
            $departmentIdsString, // lv002
            $phaseId,             // lv003
            $taskId,              // lv004
            $processStep,         // lv005
            $priority,            // lv009
            $projectId            // lv018
        );

        if (mysqli_stmt_execute($insertStmt)) {
            $assignmentId = mysqli_insert_id($this->db_link);
            mysqli_stmt_close($insertStmt);
            return [
                'success' => true,
                'message' => 'Phân công công việc thành công.',
                'data' => [
                    'assignmentId' => $assignmentId,
                    'taskId' => $taskId,
                    'departmentIds' => $departmentIdsString,
                    'priority' => $priority,
                    'processStep' => $processStep
                ]
            ];
        } else {
            $error = mysqli_stmt_error($insertStmt);
            mysqli_stmt_close($insertStmt);
            return ['success' => false, 'message' => 'Lỗi khi phân công công việc.', 'error' => $error];
        }
    }

    // Cập nhật trạng thái phân công
    public function update_task_assignment_status_data($data)
    {
        $assignmentId = isset($data['assignmentId']) ? trim($data['assignmentId']) : '';
        $isDone = isset($data['isDone']) ? (int)$data['isDone'] : 0;
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($assignmentId)) {
            return ['success' => false, 'message' => 'ID phân công không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // Kiểm tra phân công có tồn tại không
        $checkQuery = "SELECT lv001 FROM da_lh0007 WHERE lv001 = ?";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra phân công.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $assignmentId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Phân công không tồn tại.'];
        }
        mysqli_stmt_close($checkStmt);

        // Cập nhật trạng thái
        $updateQuery = "UPDATE da_lh0007 SET lv008 = ? WHERE lv001 = ?";

        $updateStmt = mysqli_prepare($this->db_link, $updateQuery);

        if (!$updateStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh cập nhật.'];
        }

        mysqli_stmt_bind_param($updateStmt, "is", $isDone, $assignmentId);

        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            return ['success' => true, 'message' => 'Cập nhật trạng thái thành công.'];
        } else {
            $error = mysqli_stmt_error($updateStmt);
            mysqli_stmt_close($updateStmt);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật trạng thái.', 'error' => $error];
        }
    }

    // Xóa phân công
    public function remove_task_assignment_data($data)
    {
        $assignmentId = isset($data['assignmentId']) ? trim($data['assignmentId']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($assignmentId)) {
            return ['success' => false, 'message' => 'ID phân công không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // Kiểm tra phân công có tồn tại không
        $checkQuery = "SELECT lv001 FROM da_lh0007 WHERE lv001 = ?";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra phân công.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $assignmentId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Phân công không tồn tại.'];
        }
        mysqli_stmt_close($checkStmt);

        // Xóa phân công
        $deleteQuery = "DELETE FROM da_lh0007 WHERE lv001 = ?";
        $deleteStmt = mysqli_prepare($this->db_link, $deleteQuery);

        if (!$deleteStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh xóa.'];
        }

        mysqli_stmt_bind_param($deleteStmt, "s", $assignmentId);

        if (mysqli_stmt_execute($deleteStmt)) {
            $affectedRows = mysqli_stmt_affected_rows($deleteStmt);
            mysqli_stmt_close($deleteStmt);

            if ($affectedRows > 0) {
                return ['success' => true, 'message' => 'Xóa phân công thành công.'];
            } else {
                return ['success' => false, 'message' => 'Không thể xóa phân công.'];
            }
        } else {
            $error = mysqli_stmt_error($deleteStmt);
            mysqli_stmt_close($deleteStmt);
            return ['success' => false, 'message' => 'Lỗi khi xóa phân công.', 'error' => $error];
        }
    }
    // Cải thiện hàm tạo giai đoạn có sẵn
    public function create_column_data($data)
    {
        $columnName = isset($data['name']) ? trim($data['name']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($columnName)) {
            return ['success' => false, 'message' => 'Tên giai đoạn không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'ID người dùng không được để trống.'];
        }

        // Kiểm tra tên trùng lặp
        $duplicateQuery = "SELECT lv001 FROM da_lh0004 WHERE lv002 = ? AND lv003 = '0'";
        $duplicateStmt = mysqli_prepare($this->db_link, $duplicateQuery);

        if (!$duplicateStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra tên trùng lặp.'];
        }

        mysqli_stmt_bind_param($duplicateStmt, "s", $columnName);
        mysqli_stmt_execute($duplicateStmt);
        $duplicateResult = mysqli_stmt_get_result($duplicateStmt);

        if (mysqli_num_rows($duplicateResult) > 0) {
            mysqli_stmt_close($duplicateStmt);
            return ['success' => false, 'message' => 'Tên giai đoạn đã tồn tại.'];
        }
        mysqli_stmt_close($duplicateStmt);

        $query = "INSERT INTO da_lh0004 (lv002, lv003, lv006, lv005) VALUES (?, '0', NOW(), ?)";
        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh SQL.'];
        }

        mysqli_stmt_bind_param($stmt, "ss", $columnName, $userId);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($this->db_link);
            mysqli_stmt_close($stmt);
            return [
                'success' => true,
                'newId' => $newId,
                'message' => 'Tạo giai đoạn "' . $columnName . '" thành công.'
            ];
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi thêm vào cơ sở dữ liệu.', 'error' => $error];
        }
    }
    // === PROJECT MANAGEMENT FUNCTIONS ===

    // Lấy danh sách tất cả dự án
    public function get_projects_data($data = [])
    {
        $query = "SELECT lv001 as id, lv002 as name, lv003 as status, lv004 as description, 
              lv005 as createdBy, lv006 as createdAt 
              FROM da_lh0002
              WHERE parent_id is null 
              ORDER BY lv006 DESC";

        $result = mysqli_query($this->db_link, $query);

        if (!$result) {
            return ['success' => false, 'message' => 'Lỗi khi truy vấn cơ sở dữ liệu.'];
        }

        $projects = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Đảm bảo status là số nguyên
            $row['status'] = (int)$row['status'];
            // Mặc định người tạo nếu không có
            if (empty($row['createdBy'])) {
                $row['createdBy'] = 'SOF001';
            }
            $projects[] = $row;
        }

        mysqli_free_result($result);
        return ['success' => true, 'data' => $projects];
    }

    // Tạo dự án mới
    public function create_project_data($data)
    {
        $projectName = isset($data['name']) ? trim($data['name']) : '';
        $description = isset($data['description']) ? trim($data['description']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($projectName)) {
            return ['success' => false, 'message' => 'Tên dự án không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // Kiểm tra tên dự án đã tồn tại chưa
        $checkQuery = "SELECT lv001 FROM da_lh0002 WHERE lv002 = ? ";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra tên dự án.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $projectName);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Tên dự án đã tồn tại.'];
        }
        mysqli_stmt_close($checkStmt);


        // Thêm dự án mới
        $insertQuery = "INSERT INTO da_lh0002 (lv002, lv003, lv004, lv005, lv006) 
                    VALUES (?, '1', ?, ?, NOW())";
        $insertStmt = mysqli_prepare($this->db_link, $insertQuery);

        if (!$insertStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh thêm dự án.'];
        }

        mysqli_stmt_bind_param($insertStmt, "sss", $projectName, $description, $userId);

        if (mysqli_stmt_execute($insertStmt)) {
            $newId = mysqli_insert_id($this->db_link);
            mysqli_stmt_close($insertStmt);
            return [
                'success' => true,
                'message' => 'Tạo dự án thành công.',
                'data' => [
                    'id' => $newId,
                    'name' => $projectName,
                    'status' => 1,
                    'description' => $description,
                    'createdBy' => $userId,
                    'createdAt' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            $error = mysqli_stmt_error($insertStmt);
            mysqli_stmt_close($insertStmt);
            return ['success' => false, 'message' => 'Lỗi khi tạo dự án.', 'error' => $error];
        }
    }

    // Cập nhật dự án
    public function update_project_data($data)
    {
        $id = isset($data['id']) ? trim($data['id']) : '';
        $projectName = isset($data['name']) ? trim($data['name']) : '';
        $description = isset($data['description']) ? trim($data['description']) : '';
        $status = isset($data['status']) ? (int)$data['status'] : 1;
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($id)) {
            return ['success' => false, 'message' => 'ID dự án không được để trống.'];
        }

        if (empty($projectName)) {
            return ['success' => false, 'message' => 'Tên dự án không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // Kiểm tra dự án có tồn tại không
        $checkQuery = "SELECT lv001 FROM da_lh0002 WHERE lv001 = ?";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra dự án.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Dự án không tồn tại.'];
        }
        mysqli_stmt_close($checkStmt);

        // Kiểm tra tên trùng lặp (trừ chính nó)
        $duplicateQuery = "SELECT lv001 FROM da_lh0004 WHERE lv002 = ? AND lv001 != ? ";
        $duplicateStmt = mysqli_prepare($this->db_link, $duplicateQuery);

        if (!$duplicateStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra tên trùng lặp.'];
        }

        mysqli_stmt_bind_param($duplicateStmt, "ss", $projectName, $id);
        mysqli_stmt_execute($duplicateStmt);
        $duplicateResult = mysqli_stmt_get_result($duplicateStmt);

        if (mysqli_num_rows($duplicateResult) > 0) {
            mysqli_stmt_close($duplicateStmt);
            return ['success' => false, 'message' => 'Tên dự án đã tồn tại.'];
        }
        mysqli_stmt_close($duplicateStmt);

        // Cập nhật dự án
        $updateQuery = "UPDATE da_lh0002 SET lv002 = ?, lv003 = ?, lv004 = ?, lv005 = ?, lv006 = NOW() 
                    WHERE lv001 = ? ";
        $updateStmt = mysqli_prepare($this->db_link, $updateQuery);

        if (!$updateStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh cập nhật.'];
        }

        mysqli_stmt_bind_param($updateStmt, "sisss", $projectName, $status, $description, $userId, $id);

        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            return ['success' => true, 'message' => 'Cập nhật dự án thành công.'];
        } else {
            $error = mysqli_stmt_error($updateStmt);
            mysqli_stmt_close($updateStmt);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật dự án.', 'error' => $error];
        }
    }

    // Xóa dự án
    public function delete_project_data($data)
    {
        $id = isset($data['id']) ? trim($data['id']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($id)) {
            return ['success' => false, 'message' => 'ID dự án không được để trống.'];
        }

        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }

        // Kiểm tra dự án có tồn tại không
        $checkQuery = "SELECT lv001, lv002 FROM da_lh0002 WHERE lv001 = ?";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra dự án.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Dự án không tồn tại.'];
        }

        $projectData = mysqli_fetch_assoc($checkResult);
        mysqli_stmt_close($checkStmt);

        // --- BẮT ĐẦU TRANSACTION ---
        mysqli_begin_transaction($this->db_link);

        try {
            // Xóa công việc liên quan (lv018 = id dự án)
            $deleteTaskQuery = "DELETE FROM da_lh0003 WHERE lv018 = ?";
            $deleteTaskStmt = mysqli_prepare($this->db_link, $deleteTaskQuery);
            if ($deleteTaskStmt) {
                mysqli_stmt_bind_param($deleteTaskStmt, "s", $id);
                mysqli_stmt_execute($deleteTaskStmt);
                mysqli_stmt_close($deleteTaskStmt);
            }

            // Xóa dự án
            $deleteProjectQuery = "DELETE FROM da_lh0002 WHERE lv001 = ?";
            $deleteProjectStmt = mysqli_prepare($this->db_link, $deleteProjectQuery);

            if (!$deleteProjectStmt) {
                mysqli_rollback($this->db_link);
                return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh xóa dự án.'];
            }

            mysqli_stmt_bind_param($deleteProjectStmt, "s", $id);
            mysqli_stmt_execute($deleteProjectStmt);
            $affectedRows = mysqli_stmt_affected_rows($deleteProjectStmt);
            mysqli_stmt_close($deleteProjectStmt);

            if ($affectedRows > 0) {
                mysqli_commit($this->db_link);
                return ['success' => true, 'message' => 'Xóa dự án "' . $projectData['lv002'] . '" và các công việc liên quan thành công.'];
            } else {
                mysqli_rollback($this->db_link);
                return ['success' => false, 'message' => 'Không thể xóa dự án.'];
            }
        } catch (Exception $e) {
            mysqli_rollback($this->db_link);
            return ['success' => false, 'message' => 'Lỗi khi xóa dữ liệu.', 'error' => $e->getMessage()];
        }
    }


    // Lấy danh sách tất cả kế hoạch
    public function get_plans_data($data = [])
    {
        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $limit = isset($data['limit']) ? (int)$data['limit'] : 20;
        $search = isset($data['search']) ? trim($data['search']) : '';
        $offset = ($page - 1) * $limit;

        // Base query
        $whereClause = "WHERE 1=1";
        $params = [];
        $types = "";

        // Add search filter
        if (!empty($search)) {
            $whereClause .= " AND (lv002 LIKE ? OR lv007 LIKE ? OR lv009 LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        // Count total records
        $countQuery = "SELECT COUNT(*) as total FROM cr_lv0004 $whereClause";
        $countStmt = mysqli_prepare($this->db_link, $countQuery);

        if (!empty($params)) {
            mysqli_stmt_bind_param($countStmt, $types, ...$params);
        }

        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $totalRecords = mysqli_fetch_assoc($countResult)['total'];
        mysqli_stmt_close($countStmt);

        // Main query with pagination
        $query = "SELECT 
                    lv001 as id,
                    lv002 as planName,
                    lv003 as createdAt,
                    lv004 as createdBy,
                    lv005 as updatedAt,
                    lv006 as updatedBy,
                    lv007 as planType,
                    lv501 as projectId,
                    lv100 as status,
                    lv009 as projectName
                  FROM cr_lv0004 
                  $whereClause
                  ORDER BY lv003 DESC 
                  LIMIT ? OFFSET ?";

        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn.'];
        }

        // Add limit and offset to params
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn kế hoạch.'];
        }

        $plans = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $plans[] = [
                'id' => $row['id'],
                'planName' => $row['planName'],
                'planType' => $row['planType'],
                'projectId' => $row['projectId'],
                'projectName' => $row['projectName'],
                'status' => (int)$row['status'],
                'createdAt' => $row['createdAt'],
                'createdBy' => $row['createdBy'],
                'updatedAt' => $row['updatedAt'],
                'updatedBy' => $row['updatedBy']
            ];
        }

        mysqli_stmt_close($stmt);

        return [
            'success' => true,
            'data' => $plans,
            'pagination' => [
                'total' => $totalRecords,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($totalRecords / $limit)
            ]
        ];
    }
    public function get_assigned_plans_data($userId, $data = [])
    {
        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $limit = isset($data['limit']) ? (int)$data['limit'] : 20;
        $search = isset($data['search']) ? trim($data['search']) : '';
        $offset = ($page - 1) * $limit;

        $whereClause = "WHERE 1=1";
        $params = [];
        $types = "";

        // Chỉ lấy kế hoạch có userId trong lv097 (dùng FIND_IN_SET hoặc LIKE đều được)
        $whereClause .= " AND (FIND_IN_SET(?, lv097) > 0 OR lv097 LIKE ?)";
        $params[] = $userId;
        $params[] = "%$userId%";
        $types .= "ss";

        // Add search filter
        if (!empty($search)) {
            $whereClause .= " AND (lv002 LIKE ? OR lv007 LIKE ? OR lv009 LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        // Count total records
        $countQuery = "SELECT COUNT(*) as total FROM cr_lv0004 $whereClause";
        $countStmt = mysqli_prepare($this->db_link, $countQuery);

        if (!empty($params)) {
            mysqli_stmt_bind_param($countStmt, $types, ...$params);
        }

        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $totalRecords = mysqli_fetch_assoc($countResult)['total'];
        mysqli_stmt_close($countStmt);

        // Main query with pagination
        $query = "SELECT 
                    lv001 as id,
                    lv002 as planName,
                    lv003 as createdAt,
                    lv004 as createdBy,
                    lv005 as updatedAt,
                    lv006 as updatedBy,
                    lv007 as planType,
                    lv501 as projectId,
                    lv008 as status,
                    lv009 as projectName,
                    lv097 as assignedUsers
                FROM cr_lv0004 
                $whereClause
                ORDER BY lv003 DESC 
                LIMIT ? OFFSET ?";

        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn.'];
        }

        // Add limit and offset to params
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn kế hoạch.'];
        }

        $plans = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $plans[] = [
                'id' => $row['id'],
                'planName' => $row['planName'],
                'planType' => $row['planType'],
                'projectId' => $row['projectId'],
                'projectName' => $row['projectName'],
                'status' => (int)$row['status'],
                'createdAt' => $row['createdAt'],
                'createdBy' => $row['createdBy'],
                'updatedAt' => $row['updatedAt'],
                'updatedBy' => $row['updatedBy'],
                'assignedUsers' => $row['assignedUsers']
            ];
        }

        mysqli_stmt_close($stmt);

        return [
            'success' => true,
            'data' => $plans,
            'pagination' => [
                'total' => $totalRecords,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($totalRecords / $limit)
            ]
        ];
    }
    public function get_employees_data($data = [])
    {
        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $limit = isset($data['limit']) ? (int)$data['limit'] : 20;
        $search = isset($data['search']) ? trim($data['search']) : '';
        $offset = ($page - 1) * $limit;

        // Base query
        $whereClause = "WHERE lv001 IS NOT NULL AND lv002 IS NOT NULL AND TRIM(lv001) != '' AND TRIM(lv002) != ''";
        $params = [];
        $types = "";

        // Add search filter
        if (!empty($search)) {
            $whereClause .= " AND (lv001 LIKE ? OR lv002 LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }

        // Count total records
        $countQuery = "SELECT COUNT(*) as total FROM hr_lv0020 $whereClause";
        $countStmt = mysqli_prepare($this->db_link, $countQuery);

        if (!empty($params)) {
            mysqli_stmt_bind_param($countStmt, $types, ...$params);
        }

        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $totalRecords = mysqli_fetch_assoc($countResult)['total'];
        mysqli_stmt_close($countStmt);

        // Main query with pagination
        $query = "SELECT 
                lv001 as employee_code,
                lv002 as employee_name
              FROM hr_lv0020
              $whereClause
              ORDER BY lv002 ASC
              LIMIT ? OFFSET ?";

        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn.'];
        }

        // Add limit and offset to params
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn danh sách nhân viên.'];
        }

        $employees = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $employees[] = [
                'employee_code' => $row['employee_code'],
                'employee_name' => $row['employee_name']
            ];
        }

        mysqli_stmt_close($stmt);

        return [
            'success' => true,
            'data' => $employees,
            'pagination' => [
                'total' => $totalRecords,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($totalRecords / $limit)
            ]
        ];
    }
    function MapTasksFromProject($project_code, $userId)
    {
        if (empty($project_code)) {
            return false;
        }
        $safe_project_code = sof_escape_string($project_code);
        $safe_user_id = sof_escape_string($userId);

        $source_tasks_sql = "SELECT D.lv004, D.lv005, D.lv007 FROM da_lh0003 D WHERE D.lv018 = '$safe_project_code'";
        $source_result = db_query($source_tasks_sql);
        if (!$source_result) return false;

        while ($task = db_fetch_array($source_result)) {

            $next_task_id = $task['lv004'];

            while (true) {
                $safe_next_task_id = sof_escape_string($next_task_id);
                $check_sql = "SELECT lv001 FROM cr_lv0003 WHERE lv001 = '$safe_next_task_id'";
                $check_result = db_query($check_sql);

                if (db_num_rows($check_result) == 0) {
                    break;
                } else {
                    $next_task_id = $this->IncrementCode($next_task_id);
                }
            }

            $final_task_id = sof_escape_string($next_task_id);
            $task_code = sof_escape_string($task['lv005']);
            $task_data_lv007 = sof_escape_string($task['lv007']);

            $sql_max_order = "SELECT MAX(lv008) AS max_order FROM cr_lv0003 WHERE lv099 = 'TASK'";
            $result_max = db_query($sql_max_order);
            $row_max = db_fetch_array($result_max);
            $new_order = ($row_max['max_order'] ? (int)$row_max['max_order'] : 0) + 1;

            $insert_sql = "INSERT INTO cr_lv0003 
								(lv001, lv002, lv004, lv003, lv099, lv009, lv005, lv006, lv007, lv008)
						   VALUES 
								('$final_task_id', '$task_code', '$task_data_lv007', 0, 'TASK', 1, '$safe_user_id', NOW(), '$task_data_lv007', '$new_order')";
            db_query($insert_sql);
        }

        return true;
    }
    public function CreatePlanTasks($plan_id, $project_code, $userId)
    {
        if (empty($plan_id) || empty($project_code)) {
            return false;
        }

        $safe_plan_id = sof_escape_string($plan_id);
        $safe_project_code = sof_escape_string($project_code);
        $safe_user_id = sof_escape_string($userId);

        $sql_insert_tasks = "
        INSERT INTO cr_lv0005 (
            lv002,           -- Mã kế hoạch
            lv501,           -- Mã công việc
            lv003,           -- Mã loại công việc
            lv004,           -- Tên công việc
            lv005,           -- Ngày hoàn thành
            lv009,           -- Người tạo
            lv010,           -- Ngày tạo
            lv011            -- Trạng thái
        )
        SELECT
            '$safe_plan_id', -- lv002
            D.lv004,         -- lv501
            D.lv004,         -- lv003
            D.lv006,         -- lv004
            D.lv019,         -- lv005
            '$safe_user_id', -- lv009
            NOW(),           -- lv010
            0                -- lv011
        FROM da_lh0003 D
        WHERE D.lv018 = '$safe_project_code'
        AND NOT EXISTS (
            SELECT 1 FROM cr_lv0005 T WHERE T.lv501 = D.lv004
        )
        ";

        return db_query($sql_insert_tasks);
    }
    // Cập nhật hàm create_plan_data trong PlanManagementController
    public function create_plan_data($data)
    {
        $planName = isset($data['planName']) ? trim($data['planName']) : '';
        $planType = isset($data['planType']) ? trim($data['planType']) : '';
        $projectId = isset($data['projectId']) ? trim($data['projectId']) : '';
        $projectName = isset($data['projectName']) ? trim($data['projectName']) : '';
        $createdBy = isset($data['createdBy']) ? trim($data['createdBy']) : '';
        $assignedEmployees = $data['assignedEmployees'] ?? [];
        $status = isset($data['status']) ? (int)$data['status'] : 0;
        $assignedEmployees = $data['assignedEmployees'] ?? [];


        if (empty($planName)) {
            return ['success' => false, 'message' => 'Tên kế hoạch không được để trống.'];
        }

        if (empty($createdBy)) {
            return ['success' => false, 'message' => 'Thông tin người tạo không hợp lệ.'];
        }

        // Tạo mã kế hoạch tự động
        $planId = $this->generatePlanId();
        if (!$planId) {
            return ['success' => false, 'message' => 'Không thể tạo mã kế hoạch.'];
        }
        if (!empty($projectId)) {
            // Nếu là dự án mẫu, clone ra dự án mới
            $projectId = $this->clone_project($projectId, $createdBy);
        }
        $currentDateTime = date('Y-m-d H:i:s');
        $assignedEmployeesString = is_array($assignedEmployees) ? implode(',', $assignedEmployees) : trim($assignedEmployees);

        $insertQuery = "INSERT INTO cr_lv0004 (lv001, lv002, lv003, lv004, lv007, lv501, lv100, lv009,lv097) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = mysqli_prepare($this->db_link, $insertQuery);

        if (!$insertStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh tạo kế hoạch.'];
        }

        mysqli_stmt_bind_param(
            $insertStmt,
            "sssssisss",
            $planId,
            $planName,
            $currentDateTime,
            $createdBy,
            $planType,
            $projectId,
            $status,
            $projectName,
            $assignedEmployeesString
        );

        if (mysqli_stmt_execute($insertStmt)) {
            mysqli_stmt_close($insertStmt);
            $this->MapTasksFromProject($projectId, $createdBy);
            $this->CreatePlanTasks($planId, $projectId, $createdBy);

            return [
                'success' => true,
                'message' => 'Tạo kế hoạch thành công.',
                'data' => [
                    'id' => $planId,
                    'planName' => $planName,
                    'planType' => $planType,
                    'projectId' => $projectId,
                    'projectName' => $projectName,
                    'status' => $status
                ]
            ];
        } else {
            $error = mysqli_stmt_error($insertStmt);
            mysqli_stmt_close($insertStmt);
            return ['success' => false, 'message' => 'Lỗi khi tạo kế hoạch.', 'error' => $error];
        }
    }

    // Thêm hàm tạo mã kế hoạch tự động
    private function generatePlanId()
    {
        // Lấy năm hiện tại
        $currentYear = date('Y');
        $yearSuffix = substr($currentYear, -2); // Lấy 2 số cuối của năm (ví dụ: 25 cho 2025)

        // Tìm số thứ tự cao nhất trong năm hiện tại
        $currentYearPattern = "%/PLAN/MP" . $yearSuffix;

        $query = "SELECT lv001 FROM cr_lv0004 
              WHERE lv001 LIKE ? 
              ORDER BY lv001 DESC 
              LIMIT 1";

        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "s", $currentYearPattern);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $nextNumber = 1; // Mặc định bắt đầu từ 1

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $lastPlanId = $row['lv001'];

            // Trích xuất số thứ tự từ mã kế hoạch cuối cùng
            // Format: 0001/PLAN/MP25
            if (preg_match('/^(\d+)\/PLAN\/MP' . $yearSuffix . '$/', $lastPlanId, $matches)) {
                $lastNumber = (int)$matches[1];
                $nextNumber = $lastNumber + 1;
            }
        }

        mysqli_stmt_close($stmt);

        // Tạo mã kế hoạch mới với format 4 chữ số
        $planId = sprintf('%04d/PLAN/MP%s', $nextNumber, $yearSuffix);

        return $planId;
    }

    // Cập nhật kế hoạch
    public function update_plan_data($data)
    {
        $planId = isset($data['id']) ? trim($data['id']) : '';
        $planName = isset($data['planName']) ? trim($data['planName']) : '';
        $planType = isset($data['planType']) ? trim($data['planType']) : '';
        $projectId = isset($data['projectId']) ? trim($data['projectId']) : '';
        $projectName = isset($data['projectName']) ? trim($data['projectName']) : '';
        $updatedBy = isset($data['updatedBy']) ? trim($data['updatedBy']) : '';
        $status = isset($data['status']) ? (int)$data['status'] : 0;
        $assignedEmployees = $data['assignedEmployees'] ?? [];

        $assignedEmployeesString = is_array($assignedEmployees) ? implode(',', $assignedEmployees) : trim($assignedEmployees);
        if (empty($planId)) {
            return ['success' => false, 'message' => 'ID kế hoạch không được để trống.'];
        }

        if (empty($planName)) {
            return ['success' => false, 'message' => 'Tên kế hoạch không được để trống.'];
        }

        if (empty($updatedBy)) {
            return ['success' => false, 'message' => 'Thông tin người cập nhật không hợp lệ.'];
        }

        // Kiểm tra kế hoạch có tồn tại không
        $checkQuery = "SELECT lv001 FROM cr_lv0004 WHERE lv001 = ?";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra kế hoạch.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $planId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Kế hoạch không tồn tại.'];
        }
        mysqli_stmt_close($checkStmt);

        $currentDateTime = date('Y-m-d H:i:s');

        $updateQuery = "UPDATE cr_lv0004 SET 
                        lv002 = ?, lv005 = ?, lv006 = ?, lv007 = ?, 
                        lv501 = ?, lv100 = ?, lv009 = ?, lv097 = ?
                        WHERE lv001 = ?";

        $updateStmt = mysqli_prepare($this->db_link, $updateQuery);

        if (!$updateStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh cập nhật.'];
        }

        mysqli_stmt_bind_param(
            $updateStmt,
            "sssssisss",
            $planName,
            $currentDateTime,
            $updatedBy,
            $planType,
            $projectId,
            $status,
            $projectName,
            $assignedEmployeesString,
            $planId
        );

        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            return ['success' => true, 'message' => 'Cập nhật kế hoạch thành công.'];
        } else {
            $error = mysqli_stmt_error($updateStmt);
            mysqli_stmt_close($updateStmt);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật kế hoạch.', 'error' => $error];
        }
    }

    // Xóa kế hoạch
    public function delete_plan_data($data)
    {
        $planId = isset($data['id']) ? trim($data['id']) : '';
        $deletedBy = isset($data['deletedBy']) ? trim($data['deletedBy']) : '';

        if (empty($planId)) {
            return ['success' => false, 'message' => 'ID kế hoạch không được để trống.'];
        }

        if (empty($deletedBy)) {
            return ['success' => false, 'message' => 'Thông tin người xóa không hợp lệ.'];
        }

        // Kiểm tra kế hoạch có tồn tại không
        $checkQuery = "SELECT lv001 FROM cr_lv0004 WHERE lv001 = ?";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra kế hoạch.'];
        }

        mysqli_stmt_bind_param($checkStmt, "s", $planId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            mysqli_stmt_close($checkStmt);
            return ['success' => false, 'message' => 'Kế hoạch không tồn tại.'];
        }
        mysqli_stmt_close($checkStmt);

        // Xóa kế hoạch
        $deleteQuery = "DELETE FROM cr_lv0004 WHERE lv001 = ?";
        $deleteStmt = mysqli_prepare($this->db_link, $deleteQuery);

        if (!$deleteStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh xóa.'];
        }

        mysqli_stmt_bind_param($deleteStmt, "s", $planId);

        if (mysqli_stmt_execute($deleteStmt)) {
            $affectedRows = mysqli_stmt_affected_rows($deleteStmt);
            mysqli_stmt_close($deleteStmt);

            if ($affectedRows > 0) {
                return ['success' => true, 'message' => 'Xóa kế hoạch thành công.'];
            } else {
                return ['success' => false, 'message' => 'Không thể xóa kế hoạch.'];
            }
        } else {
            $error = mysqli_stmt_error($deleteStmt);
            mysqli_stmt_close($deleteStmt);
            return ['success' => false, 'message' => 'Lỗi khi xóa kế hoạch.', 'error' => $error];
        }
    }

    // Lấy chi tiết kế hoạch
    public function get_plan_detail_data($data)
    {
        $planId = isset($data['id']) ? trim($data['id']) : '';

        if (empty($planId)) {
            return ['success' => false, 'message' => 'ID kế hoạch không được để trống.'];
        }

        $query = "SELECT 
                    lv001 as id,
                    lv002 as planName,
                    lv003 as createdAt,
                    lv004 as createdBy,
                    lv005 as updatedAt,
                    lv006 as updatedBy,
                    lv007 as planType,
                    lv501 as projectId,
                    lv008 as status,
                    lv009 as projectName,
                    lv097 as assigned_employees_json
                  FROM cr_lv0004 
                  WHERE lv001 = ?";


        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn.'];
        }

        mysqli_stmt_bind_param($stmt, "s", $planId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn chi tiết kế hoạch.'];
        }

        if (mysqli_num_rows($result) == 0) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Kế hoạch không tồn tại.'];
        }

        $plan = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        // Parse assigned employees
        $assignedEmployees = [];
        if (!empty($plan['assigned_employees_json'])) {
            $employeeCodes = json_decode($plan['assigned_employees_json'], true);
            if (is_array($employeeCodes) && count($employeeCodes) > 0) {
                $placeholders = str_repeat('?,', count($employeeCodes) - 1) . '?';
                $employeeSql = "SELECT lv001 as employee_code, lv002 as employee_name 
                                   FROM hr_lv0020 
                                   WHERE lv001 IN ($placeholders)
                                   ORDER BY lv002";
                $employeeStmt = $this->db_link->prepare($employeeSql);
                $employeeStmt->execute($employeeCodes);
                $assignedEmployees = $employeeStmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        $plan['assigned_employees'] = $assignedEmployees;
        return [
            'success' => true,
            'data' => [
                'id' => $plan['id'],
                'planName' => $plan['planName'],
                'planType' => $plan['planType'],
                'projectId' => $plan['projectId'],
                'projectName' => $plan['projectName'],
                'status' => (int)$plan['status'],
                'createdAt' => $plan['createdAt'],
                'createdBy' => $plan['createdBy'],
                'updatedAt' => $plan['updatedAt'],
                'updatedBy' => $plan['updatedBy'],
                'assigned_employees_json' => $plan['assigned_employees'],
            ]
        ];
    }

    // Lấy danh sách công việc của kế hoạch từ bảng cr_lv0005
    public function get_plan_tasks_data($data = [])
    {
        $planId = isset($data['planId']) ? trim($data['planId']) : '';
        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $limit = isset($data['limit']) ? (int)$data['limit'] : 20;
        $offset = ($page - 1) * $limit;

        if (empty($planId)) {
            return ['success' => false, 'message' => 'Plan ID không được để trống.'];
        }

        // Lấy danh sách công việc theo planId
        $query = "SELECT 
                cr_lv0005.lv001 as id,
                cr_lv0005.lv002 as planId,
                cr_lv0005.lv501 as taskId,
                da_lh0003.lv006 as description,
                da_lh0003.lv005 as taskName,
                da_lh0003.lv003 as phaseId,
                da_lh0003.lv013 as startDate,
                da_lh0003.lv012 as priority,
                cr_lv0005.lv005 as dueDate,
                cr_lv0005.lv006 as assigneeId,
                cr_lv0005.lv008 as status,
                cr_lv0005.lv009 as createdBy,
                cr_lv0005.lv010 as createdAt
              FROM cr_lv0005, da_lh0003 
              WHERE cr_lv0005.lv002 = ? and cr_lv0005.lv501 = da_lh0003.lv004
              ORDER BY cr_lv0005.lv010 DESC 
              LIMIT ? OFFSET ?";

        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị truy vấn.'];
        }

        mysqli_stmt_bind_param($stmt, "sii", $planId, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi truy vấn công việc.'];
        }

        $tasks = [];
        while ($row = mysqli_fetch_assoc($result)) {

            $tasks[] = [
                'id' => $row['id'],
                'planId' => $row['planId'],
                'taskTitle' => $row['taskTitle'],
                'taskId' => $row['taskId'],
                'taskName' => $row['taskName'],
                'description' => $row['description'],
                'startDate' => $row['startDate'],
                'dueDate' => $row['dueDate'],
                'assigneeId' => $row['assigneeId'],
                'priority' => (int)$row['priority'],
                'phaseId' => (int)$row['phaseId'],
                'status' => (int)$row['status'],
                'createdBy' => $row['createdBy'],
                'createdAt' => $row['createdAt']
            ];
        }

        mysqli_stmt_close($stmt);

        // Đếm tổng số records
        $countQuery = "SELECT COUNT(*) as total FROM cr_lv0004 WHERE lv002 = ?";
        $countStmt = mysqli_prepare($this->db_link, $countQuery);
        mysqli_stmt_bind_param($countStmt, "s", $planId);
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $totalRecords = mysqli_fetch_assoc($countResult)['total'];
        mysqli_stmt_close($countStmt);

        return [
            'success' => true,
            'data' => $tasks,
            'pagination' => [
                'total' => $totalRecords,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($totalRecords / $limit)
            ]
        ];
    }

    // Tạo công việc mới trong kế hoạch
    public function create_plan_task_data($data)
    {
        $planId = isset($data['planId']) ? trim($data['planId']) : '';
        $taskTitle = isset($data['taskTitle']) ? trim($data['taskTitle']) : '';
        $taskName = isset($data['taskName']) ? trim($data['taskName']) : '';
        $description = isset($data['description']) ? trim($data['description']) : null;
        $dueDate = isset($data['dueDate']) ? trim($data['dueDate']) : null;
        $assigneeId = isset($data['assigneeId']) ? trim($data['assigneeId']) : null;
        $status = isset($data['status']) ? (int)$data['status'] : 0;
        $createdBy = isset($data['createdBy']) ? trim($data['createdBy']) : '';

        if (empty($planId)) {
            return ['success' => false, 'message' => 'Plan ID không được để trống.'];
        }

        if (empty($taskTitle)) {
            return ['success' => false, 'message' => 'Tiêu đề công việc không được để trống.'];
        }

        if (empty($createdBy)) {
            return ['success' => false, 'message' => 'Thông tin người tạo không hợp lệ.'];
        }

        // Tạo ID cho task
        $taskRecordId = 'TASK_' . time() . '_' . rand(1000, 9999);
        $taskId = 'T' . time();
        $currentDateTime = date('Y-m-d H:i:s');

        $insertQuery = "INSERT INTO cr_lv0005 (lv001, lv002, lv003, lv501, lv004, lv005, lv006, lv007, lv008, lv009, lv010) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = mysqli_prepare($this->db_link, $insertQuery);

        if (!$insertStmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh tạo công việc.'];
        }

        mysqli_stmt_bind_param(
            $insertStmt,
            "sssssssssss",
            $taskRecordId,
            $planId,
            $taskTitle,
            $taskId,
            $taskName,
            $dueDate,
            $description,
            $assigneeId,
            $status,
            $createdBy,
            $currentDateTime
        );

        if (mysqli_stmt_execute($insertStmt)) {
            mysqli_stmt_close($insertStmt);
            return [
                'success' => true,
                'message' => 'Tạo công việc thành công.',
                'data' => [
                    'id' => $taskRecordId,
                    'planId' => $planId,
                    'taskTitle' => $taskTitle,
                    'taskId' => $taskId,
                    'taskName' => $taskName
                ]
            ];
        } else {
            $error = mysqli_stmt_error($insertStmt);
            mysqli_stmt_close($insertStmt);
            return ['success' => false, 'message' => 'Lỗi khi tạo công việc.', 'error' => $error];
        }
    }
    /**
     * Tạo icon đánh giá mới.
     */
    public function create_evaluation_icon_data($data)
    {
        $projectId = isset($data['projectId']) ? $data['projectId'] : '0';
        $name = isset($data['name']) ? trim($data['name']) : '';
        $class = isset($data['class']) ? trim($data['class']) : '';
        $color = isset($data['color']) ? trim($data['color']) : '';

        if (empty($name) || empty($class) || empty($color)) {
            return ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin.'];
        }

        $query = "INSERT INTO da_lh0006 (lv018, lv005, lv006, lv007) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "ssss", $projectId, $name, $class, $color);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi tạo icon.', 'error' => $error];
        }
    }

    /**
     * Thêm một bình luận mới.
     */
    public function post_comment_data($data)
    {
        $taskId = isset($data['taskId']) ? (int)$data['taskId'] : 0;
        $userId = isset($data['userId']) ? trim($data['userId']) : '';
        $commentText = isset($data['commentText']) ? trim($data['commentText']) : '';

        if ($taskId <= 0 || empty($userId) || empty($commentText)) {
            return ['success' => false, 'message' => 'Thông tin không hợp lệ.'];
        }

        $query = "INSERT INTO da_task_comments (task_id, user_id, comment_text) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "iss", $taskId, $userId, $commentText);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi lưu bình luận.'];
        }
    }

    // ===================================================================
    // ==                  CÁC HÀM CẬP NHẬT (UPDATE)                    ==
    // ===================================================================

    /**
     * Di chuyển công việc (task) sang cột mới và ghi lại lịch sử hành động.
     * Hàm này được cập nhật để sử dụng Master Task ID và lưu log vào bảng cr_lv0090.
     */
    public function move_task_data($data)
    {
        // Lấy dữ liệu từ input đã được decode sẵn
        $masterTaskId = isset($data['taskId']) ? (int)$data['taskId'] : 0;
        $newColumnId = isset($data['newColumnId']) ? (int)$data['newColumnId'] : 0;
        $projectId =  isset($data['projectId']) ? (int)$data['projectId'] : 0;

        // Lấy thời gian thực thi từ frontend, nếu không có thì dùng thời gian hiện tại
        $kanbanTaskId = isset($data['kanbanTaskId']) ? (int)$data['kanbanTaskId'] : 0;
        if ($masterTaskId <= 0 || $newColumnId <= 0) {
            return ['success' => false, 'message' => 'ID công việc hoặc cột không hợp lệ.'];
        }

        $oldColumnId = 0;

        $old_col_query = "SELECT t_kanban.lv003 as oldId 
                      FROM da_lh0003 AS t_kanban
                      JOIN cr_lv0005 AS t_master ON t_kanban.lv004 = t_master.lv501
                      WHERE t_master.lv001 = ? AND t_kanban.lv018 = ?";

        $stmt_old = mysqli_prepare($this->db_link, $old_col_query);
        mysqli_stmt_bind_param($stmt_old, "ii", $masterTaskId, $projectId);
        mysqli_stmt_execute($stmt_old);
        $result_old = mysqli_stmt_get_result($stmt_old);
        if ($row_old = mysqli_fetch_assoc($result_old)) {
            $oldColumnId = (int)$row_old['oldId'];
        }
        mysqli_stmt_close($stmt_old);

        if ($oldColumnId === 0) {
            return ['success' => false, 'message' => 'Không tìm thấy công việc tương ứng.'];
        }

        // Bắt đầu transaction
        mysqli_autocommit($this->db_link, false);
        $success = true;

        // B2: Cập nhật vị trí cột mới cho công việc
        $update_query = "UPDATE da_lh0003 t_kanban
                     JOIN cr_lv0005 t_master ON t_kanban.lv004 = t_master.lv501
                     SET t_kanban.lv003 = ?
                     WHERE t_master.lv001 = ? AND t_kanban.lv018 = ?";
        $stmt_update = mysqli_prepare($this->db_link, $update_query);
        mysqli_stmt_bind_param($stmt_update, "iii", $newColumnId, $masterTaskId, $projectId);

        if (!mysqli_stmt_execute($stmt_update)) {
            $success = false;
        }
        mysqli_stmt_close($stmt_update);
        // Tìm ID người đảm nhận hiện tại ở cột CŨ để gán lại cho cột MỚI
        $current_assignee_id = null;
        if ($success && $oldColumnId > 0) {
            $find_assignee_query = "SELECT lv004 FROM da_lh0008 WHERE lv001 = ? AND lv002 = ? LIMIT 1";
            $stmt_find_assignee = mysqli_prepare($this->db_link, $find_assignee_query);
            mysqli_stmt_bind_param($stmt_find_assignee, "ii", $masterTaskId, $oldColumnId);
            mysqli_stmt_execute($stmt_find_assignee);
            if ($row_assignee = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_assignee))) {
                $current_assignee_id = $row_assignee['lv004'];
            }
            mysqli_stmt_close($stmt_find_assignee);
        }
        if ($success) {
            $update_dept_query = "UPDATE da_lh0007 SET lv003 = ? WHERE lv004 = ?";
            $stmt_update_pos = mysqli_prepare($this->db_link, $update_dept_query);
            mysqli_stmt_bind_param($stmt_update_pos, "ii", $newColumnId, $masterTaskId);
            if (!mysqli_stmt_execute($stmt_update_pos)) {
                $success = false;
            }
            mysqli_stmt_close($stmt_update_pos);
        }
        $current_assignee_id = null;
        $current_dept_id = null; // Cần lấy thêm Department ID để gán lại

        if ($success && $oldColumnId > 0) {
            // Lấy cả lv003 (Phòng ban) và lv004 (User)
            $find_assignee_query = "SELECT lv003, lv004 FROM da_lh0008 WHERE lv001 = ? AND lv002 = ? LIMIT 1";
            $stmt_find_assignee = mysqli_prepare($this->db_link, $find_assignee_query);
            mysqli_stmt_bind_param($stmt_find_assignee, "ii", $masterTaskId, $oldColumnId);
            mysqli_stmt_execute($stmt_find_assignee);
            if ($row_assignee = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_assignee))) {
                $current_assignee_id = $row_assignee['lv004'];
                $current_dept_id = $row_assignee['lv003'];
            }
            mysqli_stmt_close($stmt_find_assignee);
        }

        // Cập nhật bảng mapping da_lh0007 (Giữ nguyên - lưu ý lv004 ở đây là MasterID hay KanbanID tuỳ DB của bạn)
        if ($success) {
            // Giả định lv004 trong da_lh0007 là Master Task ID như code cũ của bạn
            $update_dept_query = "UPDATE da_lh0007 SET lv003 = ? WHERE lv004 = ? AND lv018 = ?";
            $stmt_update_pos = mysqli_prepare($this->db_link, $update_dept_query);
            // Thêm projectId vào điều kiện để chính xác hơn
            mysqli_stmt_bind_param($stmt_update_pos, "iii", $newColumnId, $kanbanTaskId, $projectId); 
            // Lưu ý: Tôi đổi masterTaskId thành kanbanTaskId ở đây vì bảng da_lh0007 thường map theo KanbanID (lv004). 
            // Nếu bảng bạn map theo MasterID thì đổi lại biến $masterTaskId.
            
            if (!mysqli_stmt_execute($stmt_update_pos)) {
                // $success = false; // Có thể không cần fail nếu update này không quan trọng
            }
            mysqli_stmt_close($stmt_update_pos);
        }

        // BƯỚC MỚI: Gán lại người cũ vào cột MỚI
        if ($success && !empty($current_assignee_id) && !empty($current_dept_id)) {
            // Dùng REPLACE INTO để nếu có rồi thì update, chưa có thì insert
            $reassign_query = "REPLACE INTO da_lh0008 (lv001, lv002, lv003, lv004) VALUES (?, ?, ?, ?)";
            $stmt_reassign = mysqli_prepare($this->db_link, $reassign_query);
            mysqli_stmt_bind_param($stmt_reassign, "iiss", $masterTaskId, $newColumnId, $current_dept_id, $current_assignee_id);
            
            if (!mysqli_stmt_execute($stmt_reassign)) {
                $success = false; // Nếu gán lại thất bại thì rollback
            }
            mysqli_stmt_close($stmt_reassign);
        }

        // ---------------------------------------------------------------------
        // KẾT THÚC SỬA ĐỔI
        // ---------------------------------------------------------------------

        // B3: Ghi lại lịch sử nếu việc cập nhật thành công
        if ($success) {
            // Lấy tên của cả cột cũ và cột mới để log chi tiết hơn
            $oldColumnName = 'Không rõ';
            $newColumnName = 'Không rõ';
            $names_query = "SELECT lv001, lv002 FROM da_lh0004 WHERE lv001 IN (?, ?)";
            $stmt_names = mysqli_prepare($this->db_link, $names_query);
            mysqli_stmt_bind_param($stmt_names, "ii", $oldColumnId, $newColumnId);
            mysqli_stmt_execute($stmt_names);
            $names_result = mysqli_stmt_get_result($stmt_names);
            while ($name_row = mysqli_fetch_assoc($names_result)) {
                if ($name_row['lv001'] == $oldColumnId) $oldColumnName = $name_row['lv002'];
                if ($name_row['lv001'] == $newColumnId) $newColumnName = $name_row['lv002'];
            }
            mysqli_stmt_close($stmt_names);

            // Tạo thông báo lịch sử
            $history_message = "Di chuyển từ '" . mysqli_real_escape_string($this->db_link, $oldColumnName) . "' sang '" . mysqli_real_escape_string($this->db_link, $newColumnName) . "'";
            $creatorId = 'SOF001'; // ID người thực hiện hành động, có thể thay bằng user đang đăng nhập

            // Ghi lịch sử vào cr_lv0090
            $history_query = "INSERT INTO cr_lv0090 (lv002, lv004, lv015, lv008, lv005) VALUES (?, ?, NOW(), ?, NOW())";
            $stmt_history = mysqli_prepare($this->db_link, $history_query);
            mysqli_stmt_bind_param($stmt_history, "iss", $masterTaskId, $history_message, $creatorId);
            if (!mysqli_stmt_execute($stmt_history)) {
                $success = false;
            }
            mysqli_stmt_close($stmt_history);
        }

        // B4: Hoàn tất transaction
        if ($success) {
            mysqli_commit($this->db_link);
            $response = ['success' => true];
        } else {
            mysqli_rollback($this->db_link);
            $response = ['success' => false, 'message' => 'Lỗi khi cập nhật cơ sở dữ liệu.'];
        }

        mysqli_autocommit($this->db_link, true);
        return $response;
    }

    /**
     * Xử lý đặc biệt khi kéo task vào cột "Đang thực hiện" (lv003=8, lv008=2)
     * Không thay đổi cột gốc của công việc, chỉ gán người thực hiện
     */
    public function move_to_in_progress_data($data)
    {
        $kanbanTaskId = isset($data['kanbanTaskId']) ? (int)$data['kanbanTaskId'] : 0;
        $oldColumnId = isset($data['oldColumnId']) ? (int)$data['oldColumnId'] : 0;
        $departmentId = isset($data['departmentId']) ? trim($data['departmentId']) : '';
        $projectId = isset($data['projectId']) ? trim($data['projectId']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : ($_SESSION['userlogin_smcd'] ?? '');
        $user_role = isset($data['user_role']) ? trim($data['user_role']) : 'user';

        // Tự động tìm Project ID nếu thiếu
        if (empty($projectId) || $projectId === 'null') {
            $find_project_query = "SELECT lv018 FROM da_lh0003 WHERE lv001 = ? LIMIT 1";
            $stmt_find_project = mysqli_prepare($this->db_link, $find_project_query);
            mysqli_stmt_bind_param($stmt_find_project, "i", $kanbanTaskId);
            mysqli_stmt_execute($stmt_find_project);
            if ($row_project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_project))) {
                $projectId = $row_project['lv018'];
            }
            mysqli_stmt_close($stmt_find_project);
        }

        if ($kanbanTaskId <= 0 || empty($departmentId) || empty($userId) || empty($projectId)) {
            return ['success' => false, 'message' => 'Thiếu thông tin để di chuyển vào trạng thái đang thực hiện.'];
        }

        mysqli_autocommit($this->db_link, false);

        try {
            // Tìm master task ID
            $masterTaskId = null;
            $find_master_id_query = "SELECT t_master.lv001
                    FROM da_lh0003 AS t_kanban
                    JOIN cr_lv0004 AS t_project ON t_kanban.lv018 = t_project.lv501
                    JOIN cr_lv0005 AS t_master ON t_project.lv001 = t_master.lv002 AND t_kanban.lv004 = t_master.lv501 
                    WHERE t_kanban.lv001 = ? AND t_kanban.lv018 = ?;";
            $stmt_find_m = mysqli_prepare($this->db_link, $find_master_id_query);
            mysqli_stmt_bind_param($stmt_find_m, "ii", $kanbanTaskId, $projectId);
            mysqli_stmt_execute($stmt_find_m);
            if ($row_m = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_m))) {
                $masterTaskId = $row_m['lv001'];
            }
            mysqli_stmt_close($stmt_find_m);

            if (!$masterTaskId) {
                throw new Exception('Không tìm thấy master task ID.');
            }

            // Xử lý record cho cột "Đang thực hiện" (lv008=2)
            $find_all_related_query = "SELECT lv001, lv003, lv008 FROM da_lh0007 WHERE lv004 = ? AND lv018 = ? AND FIND_IN_SET(?, lv002) > 0 AND (lv003 = 8 OR lv008 = 2)";
            $stmt_find_all = mysqli_prepare($this->db_link, $find_all_related_query);
            mysqli_stmt_bind_param($stmt_find_all, "iss", $kanbanTaskId, $projectId, $departmentId);
            mysqli_stmt_execute($stmt_find_all);
            $all_related_result = mysqli_stmt_get_result($stmt_find_all);

            $has_in_progress_record = false;
            $record_to_update = null;

            while ($row = mysqli_fetch_assoc($all_related_result)) {
                if (!$has_in_progress_record) {
                    $record_to_update = $row['lv001'];
                    $has_in_progress_record = true;
                } else {
                    // Xóa record thừa
                    $delete_extra_query = "DELETE FROM da_lh0007 WHERE lv001 = ?";
                    $stmt_delete = mysqli_prepare($this->db_link, $delete_extra_query);
                    mysqli_stmt_bind_param($stmt_delete, "i", $row['lv001']);
                    mysqli_stmt_execute($stmt_delete);
                    mysqli_stmt_close($stmt_delete);
                }
            }
            mysqli_stmt_close($stmt_find_all);

            if ($has_in_progress_record) {
                $update_in_progress_query = "UPDATE da_lh0007 SET lv003 = 8, lv008 = 2 WHERE lv001 = ?";
                $stmt_update = mysqli_prepare($this->db_link, $update_in_progress_query);
                mysqli_stmt_bind_param($stmt_update, "i", $record_to_update);
                if (!mysqli_stmt_execute($stmt_update)) {
                    throw new Exception('Lỗi khi cập nhật trạng thái đang thực hiện.');
                }
                mysqli_stmt_close($stmt_update);
            } else {
                $insert_in_progress_query = "INSERT INTO da_lh0007 (lv002, lv003, lv004, lv008, lv018) VALUES (?, 8, ?, 2, ?)";
                $stmt_insert = mysqli_prepare($this->db_link, $insert_in_progress_query);
                mysqli_stmt_bind_param($stmt_insert, "sis", $departmentId, $kanbanTaskId, $projectId);
                if (!mysqli_stmt_execute($stmt_insert)) {
                    throw new Exception('Lỗi khi tạo record đang thực hiện.');
                }
                mysqli_stmt_close($stmt_insert);
            }

            // -------------------------------------------------------------------------
            // Chỉ thực hiện gán nếu KHÔNG PHẢI admin VÀ KHÔNG PHẢI manager.
            // Nếu là admin/manager => Không làm gì cả => Giữ nguyên người cũ (nếu có).
            // -------------------------------------------------------------------------
            $assignee_to_set = null;

            if ($user_role !== 'admin' && $user_role !== 'manager') {
                // Nếu là User thường: Tự nhận việc về mình
                $assignee_to_set = $userId;
            } else {
                // Nếu là Admin/Manager: Cần tìm người cũ để gán lại (giữ nguyên)
                
                // 1. Tìm trong bảng phân công giai đoạn cũ (da_lh0008)
                if ($oldColumnId > 0) {
                    $find_assignee_query = "SELECT lv004 FROM da_lh0008 WHERE lv001 = ? AND lv002 = ? AND lv003 = ? LIMIT 1";
                    $stmt_find_assignee = mysqli_prepare($this->db_link, $find_assignee_query);
                    mysqli_stmt_bind_param($stmt_find_assignee, "iis", $masterTaskId, $oldColumnId, $departmentId);
                    mysqli_stmt_execute($stmt_find_assignee);
                    if ($row_assignee = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_assignee))) {
                        $assignee_to_set = $row_assignee['lv004'];
                    }
                    mysqli_stmt_close($stmt_find_assignee);
                }

                // 2. Nếu không thấy, lấy Assignee chính từ bảng task (da_lh0003) - Fallback
                if (empty($assignee_to_set)) {
                    $find_main_assignee = "SELECT lv016 FROM da_lh0003 WHERE lv001 = ? LIMIT 1";
                    $stmt_main = mysqli_prepare($this->db_link, $find_main_assignee);
                    mysqli_stmt_bind_param($stmt_main, "i", $kanbanTaskId);
                    mysqli_stmt_execute($stmt_main);
                    if ($row_main = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_main))) {
                        $assignee_to_set = $row_main['lv016'];
                    }
                    mysqli_stmt_close($stmt_main);
                }
            }

            // [ACTION] Thực hiện gán vào bảng da_lh0008 cho cột "Đang thực hiện" (ID=8)
            // Chỉ gán nếu xác định được người (nếu task vốn chưa ai làm thì admin kéo sang vẫn là chưa ai làm)
            if (!empty($assignee_to_set)) {
                $assign_query = "REPLACE INTO da_lh0008 (lv001, lv002, lv003, lv004) VALUES (?, 8, ?, ?)";
                $stmt_assign = mysqli_prepare($this->db_link, $assign_query);
                mysqli_stmt_bind_param($stmt_assign, "iss", $masterTaskId, $departmentId, $assignee_to_set);
                if (!mysqli_stmt_execute($stmt_assign)) {
                    throw new Exception('Lỗi khi gán người thực hiện.');
                }
                mysqli_stmt_close($stmt_assign);
            }
            // -------------------------------------------------------------------------

            // Cập nhật ngày bắt đầu
            $update_start_date_query = "UPDATE da_lh0003 SET lv013 = COALESCE(lv013, NOW()) WHERE lv001 = ?";
            $stmt_start_date = mysqli_prepare($this->db_link, $update_start_date_query);
            mysqli_stmt_bind_param($stmt_start_date, "i", $kanbanTaskId);
            mysqli_stmt_execute($stmt_start_date);
            mysqli_stmt_close($stmt_start_date);

            // Log
            $log_message = "Bắt đầu thực hiện công việc";
            $log_query = "INSERT INTO cr_lv0090 (lv002, lv004, lv015, lv008, lv005) VALUES (?, ?, NOW(), ?, NOW())";
            $stmt_log = mysqli_prepare($this->db_link, $log_query);
            mysqli_stmt_bind_param($stmt_log, "iss", $masterTaskId, $log_message, $userId);
            mysqli_stmt_execute($stmt_log);
            mysqli_stmt_close($stmt_log);

            mysqli_commit($this->db_link);

            // Cập nhật thông báo trả về
            $message = ($user_role === 'admin' || $user_role === 'manager')
                ? 'Đã chuyển trạng thái (giữ nguyên người thực hiện cũ).'
                : 'Đã chuyển vào trạng thái đang thực hiện thành công.';

            return ['success' => true, 'message' => $message];

        } catch (Exception $e) {
            mysqli_rollback($this->db_link);
            return ['success' => false, 'message' => $e->getMessage()];
        } finally {
            mysqli_autocommit($this->db_link, true);
        }
    }

    public function move_task_for_user_data($data)
    {
        // B1: Lấy các tham số cần thiết
        $kanbanTaskId = isset($data['kanbanTaskId']) ? (int)$data['kanbanTaskId'] : 0;
        $oldColumnId = isset($data['oldColumnId']) ? (int)$data['oldColumnId'] : 0;
        $newColumnId = isset($data['newColumnId']) ? (int)$data['newColumnId'] : 0;
        $departmentId = isset($data['departmentId']) ? trim($data['departmentId']) : '';
        $projectId = isset($data['projectId']) ? trim($data['projectId']) : '';
        // Lấy ID người dùng đang thực hiện thao tác từ session
        $userId = $_SESSION['userlogin_smcd'] ?? '';
        $user_role = isset($data['user_role']) ? trim($data['user_role']) : '';
        if ($kanbanTaskId <= 0 || $newColumnId <= 0 || empty($departmentId) || empty($userId)) {
            return ['success' => false, 'message' => 'Thiếu thông tin để di chuyển (task, cột mới, phòng ban, user).'];
        }

        // B1.5: NẾU KHÔNG CÓ PROJECT ID (trường hợp tổng quan phòng ban), TỰ ĐỘNG TÌM
        if (empty($projectId) || $projectId === 'null') {
            $find_project_query = "SELECT lv018 FROM da_lh0003 WHERE lv001 = ? LIMIT 1";
            $stmt_find_project = mysqli_prepare($this->db_link, $find_project_query);
            mysqli_stmt_bind_param($stmt_find_project, "i", $kanbanTaskId);
            mysqli_stmt_execute($stmt_find_project);
            if ($row_project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_project))) {
                $projectId = $row_project['lv018'];
            }
            mysqli_stmt_close($stmt_find_project);

            if (empty($projectId)) {
                return ['success' => false, 'message' => 'Không thể xác định dự án cho công việc này.'];
            }
        }

        // B2: KIỂM TRA XEM CỘT CŨ CÓ PHẢI LÀ CỘT "ĐANG THỰC HIỆN" KHÔNG
        $is_from_in_progress = false;
        if ($oldColumnId > 0) {
            $check_old_in_progress_query = "SELECT lv008 FROM da_lh0007 WHERE lv018 = ? AND lv003 = ? AND FIND_IN_SET(?, lv002) > 0 LIMIT 1";
            $stmt_check_old = mysqli_prepare($this->db_link, $check_old_in_progress_query);
            mysqli_stmt_bind_param($stmt_check_old, "sis", $projectId, $oldColumnId, $departmentId);
            mysqli_stmt_execute($stmt_check_old);
            if ($row_old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check_old))) {
                if ($row_old['lv008'] == 2) {
                    $is_from_in_progress = true;
                }
            }
            mysqli_stmt_close($stmt_check_old);
        }

        // B3: KIỂM TRA XEM CỘT MỚI CÓ PHẢI LÀ CỘT "DONE" KHÔNG
        $is_done_column = false;
        // Chúng ta kiểm tra cờ lv008 trong bảng da_lh0007 cho cột mới này
        $check_done_query = "SELECT lv008 FROM da_lh0007 WHERE lv018 = ? AND lv003 = ? AND FIND_IN_SET(?, lv002) > 0 LIMIT 1";
        $stmt_check = mysqli_prepare($this->db_link, $check_done_query);
        mysqli_stmt_bind_param($stmt_check, "sis", $projectId, $newColumnId, $departmentId);
        mysqli_stmt_execute($stmt_check);
        if ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check))) {
            if ($row['lv008'] == 1) {
                $is_done_column = true;
            }
        }
        mysqli_stmt_close($stmt_check);

        // B4: RẼ NHÁNH LOGIC DỰA TRÊN KẾT QUẢ KIỂM TRA
        if ($is_done_column) {
            // NẾU LÀ CỘT DONE -> Chuyển hướng sang hàm xử lý hoàn thành
            $completion_data = [
                'kanbanTaskId' => $kanbanTaskId,
                'departmentId' => $departmentId,
                'projectId' => $projectId,
                'isCompleted' => 1, // Đánh dấu là đang muốn "Hoàn thành"
                'userId' => $userId,
                'user_role' => $user_role,
                'isFromInProgress' => $is_from_in_progress // Thêm flag để biết có đang từ cột "Đang thực hiện" không
            ];

            // Gọi hàm toggle_completion_for_dept_data và trả về kết quả của nó
            return $this->toggle_completion_for_dept_data($completion_data);
        } else {
            // NẾU LÀ CỘT THƯỜNG -> Giữ nguyên logic di chuyển đơn giản
            mysqli_autocommit($this->db_link, false);
            $success = true;

            // Tìm master task ID tương ứng
            $masterTaskId = null;
            $find_master_id_query = "SELECT t_master.lv001 FROM cr_lv0005 AS t_master JOIN da_lh0003 AS t_kanban ON t_master.lv501 = t_kanban.lv004 WHERE t_kanban.lv001 = ? LIMIT 1";
            $stmt_find_m = mysqli_prepare($this->db_link, $find_master_id_query);
            mysqli_stmt_bind_param($stmt_find_m, "i", $kanbanTaskId);
            mysqli_stmt_execute($stmt_find_m);
            if ($row_m = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_m))) {
                $masterTaskId = $row_m['lv001'];
            }
            mysqli_stmt_close($stmt_find_m);

            if (!$masterTaskId) {
                $success = false;
            }

            // Tìm ID người đảm nhận hiện tại ở cột CŨ để gán lại cho cột MỚI
            $current_assignee_id = null;
            if ($success && $oldColumnId > 0) {
                $find_assignee_query = "SELECT lv004 FROM da_lh0008 WHERE lv001 = ? AND lv002 = ? AND lv003 = ? LIMIT 1";
                $stmt_find_assignee = mysqli_prepare($this->db_link, $find_assignee_query);
                mysqli_stmt_bind_param($stmt_find_assignee, "iis", $masterTaskId, $oldColumnId, $departmentId);
                mysqli_stmt_execute($stmt_find_assignee);
                if ($row_assignee = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_assignee))) {
                    $current_assignee_id = $row_assignee['lv004'];
                }
                mysqli_stmt_close($stmt_find_assignee);
            }

            // XỬ LÝ ĐỐI VỚI CỘT "ĐANG THỰC HIỆN": Nếu kéo từ cột đang thực hiện về cột thường
            if ($success && $is_from_in_progress) {
                // XÓA LUÔN record có lv003 = 8 và lv008 = 2 của công việc thuộc phòng ban đó
                $delete_in_progress_query = "DELETE FROM da_lh0007 WHERE lv004 = ? AND lv003 = 8 AND lv008 = 2 AND FIND_IN_SET(?, lv002) > 0 AND lv018 = ?";
                $stmt_delete_ip = mysqli_prepare($this->db_link, $delete_in_progress_query);
                mysqli_stmt_bind_param($stmt_delete_ip, "iss", $kanbanTaskId, $departmentId, $projectId);
                if (!mysqli_stmt_execute($stmt_delete_ip)) {
                    $success = false;
                }
                mysqli_stmt_close($stmt_delete_ip);

                // Kiểm tra xem đã có record cho cột đích chưa, nếu chưa thì tạo mới
                $check_target_column_query = "SELECT lv001 FROM da_lh0007 WHERE lv004 = ? AND lv003 = ? AND FIND_IN_SET(?, lv002) > 0 AND lv018 = ? LIMIT 1";
                $stmt_check_target = mysqli_prepare($this->db_link, $check_target_column_query);
                mysqli_stmt_bind_param($stmt_check_target, "iiss", $kanbanTaskId, $newColumnId, $departmentId, $projectId);
                mysqli_stmt_execute($stmt_check_target);
                $target_record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check_target));
                mysqli_stmt_close($stmt_check_target);

                if (!$target_record) {
                    // Tạo record mới cho cột đích
                    $insert_target_query = "INSERT INTO da_lh0007 (lv002, lv003, lv004, lv008, lv018) VALUES (?, ?, ?, 0, ?)";
                    $stmt_insert_target = mysqli_prepare($this->db_link, $insert_target_query);
                    mysqli_stmt_bind_param($stmt_insert_target, "siis", $departmentId, $newColumnId, $kanbanTaskId, $projectId);
                    if (!mysqli_stmt_execute($stmt_insert_target)) {
                        $success = false;
                    }
                    mysqli_stmt_close($stmt_insert_target);
                } else {
                    // Nếu đã có record cho cột đích, cập nhật lv008 = 0 để đảm bảo nó là cột thường
                    $update_target_query = "UPDATE da_lh0007 SET lv008 = 0 WHERE lv001 = ?";
                    $stmt_update_target = mysqli_prepare($this->db_link, $update_target_query);
                    mysqli_stmt_bind_param($stmt_update_target, "i", $target_record['lv001']);
                    mysqli_stmt_execute($stmt_update_target);
                    mysqli_stmt_close($stmt_update_target);
                }
            } else {
                // Cập nhật vị trí mới của công việc trong da_lh0007 (chỉ khi KHÔNG phải từ cột đang thực hiện)
                if ($success) {
                    // QUAN TRỌNG: Chỉ cập nhật record của cột cũ, KHÔNG động vào cột "Đang thực hiện" (cột 8)
                    if ($oldColumnId > 0) {
                        $update_dept_query = "UPDATE da_lh0007 SET lv003 = ? WHERE lv004 = ? AND lv003 = ? AND FIND_IN_SET(?, lv002) > 0 AND lv018 = ?";
                        $stmt_update_pos = mysqli_prepare($this->db_link, $update_dept_query);
                        mysqli_stmt_bind_param($stmt_update_pos, "iiiss", $newColumnId, $kanbanTaskId, $oldColumnId, $departmentId, $projectId);
                        if (!mysqli_stmt_execute($stmt_update_pos)) {
                            $success = false;
                        }
                        mysqli_stmt_close($stmt_update_pos);
                    } else {
                        // Nếu không có oldColumnId, tìm record đầu tiên không phải cột 8 để cập nhật
                        $update_dept_query = "UPDATE da_lh0007 SET lv003 = ? WHERE lv004 = ? AND lv003 != 8 AND FIND_IN_SET(?, lv002) > 0 AND lv018 = ? LIMIT 1";
                        $stmt_update_pos = mysqli_prepare($this->db_link, $update_dept_query);
                        mysqli_stmt_bind_param($stmt_update_pos, "iiss", $newColumnId, $kanbanTaskId, $departmentId, $projectId);
                        if (!mysqli_stmt_execute($stmt_update_pos)) {
                            $success = false;
                        }
                        mysqli_stmt_close($stmt_update_pos);
                    }
                }
            }
            if ($success) {
                $update_query = "UPDATE da_lh0003 t_kanban
                         JOIN cr_lv0005 t_master ON t_kanban.lv004 = t_master.lv501
                         SET t_kanban.lv003 = ?
                         WHERE t_master.lv001 = ?";
                $stmt_update = mysqli_prepare($this->db_link, $update_query);
                mysqli_stmt_bind_param($stmt_update, "ii", $newColumnId, $masterTaskId);
                if (!mysqli_stmt_execute($stmt_update)) {
                    $success = false;
                }
                mysqli_stmt_close($stmt_update);
            }

            // Gán lại người đảm nhận cũ (nếu có) vào vị trí MỚI
            if ($success && $current_assignee_id) {
                $assign_query = "REPLACE INTO da_lh0008 (lv001, lv002, lv003, lv004) VALUES (?, ?, ?, ?)";
                $stmt_assign = mysqli_prepare($this->db_link, $assign_query);
                mysqli_stmt_bind_param($stmt_assign, "iiss", $masterTaskId, $newColumnId, $departmentId, $current_assignee_id);
                if (!mysqli_stmt_execute($stmt_assign)) {
                    $success = false;
                }
                mysqli_stmt_close($stmt_assign);
            }

            // Hoàn tất giao dịch
            if ($success) {
                mysqli_commit($this->db_link);
                return ['success' => true];
            } else {
                mysqli_rollback($this->db_link);
                return ['success' => false, 'message' => 'Lỗi khi cập nhật CSDL.'];
            }
            mysqli_autocommit($this->db_link, true);
        }
    }


    /**
     * Cập nhật thứ tự các cột.
     */
    public function update_column_order_data($data, $projectId)
    {
        $orderedIds = isset($data['order']) ? $data['order'] : [];

        if ($projectId <= 0 || empty($orderedIds)) {
            return ['success' => false, 'message' => 'Thông tin không hợp lệ.'];
        }

        $query = "UPDATE da_lh0005 SET lv003 = ? WHERE lv001 = ? AND lv002 = ?";
        $stmt = mysqli_prepare($this->db_link, $query);

        foreach ($orderedIds as $index => $stageId) {
            $order = $index + 1;
            mysqli_stmt_bind_param($stmt, "isi", $order, $projectId, $stageId);
            mysqli_stmt_execute($stmt);
        }

        mysqli_stmt_close($stmt);
        return ['success' => true];
    }
    // === TASK SELF ASSIGNMENT FUNCTION ===

    // Nhận việc - sử dụng hàm assign_user_to_stage_data có sẵn
   public function take_task_data($data)
    {
        $taskId = isset($data['taskId']) ? trim($data['taskId']) : '';
        $kanbanTaskId = isset($data['kanbanTaskId']) ? trim($data['kanbanTaskId']) : '';
        $userId = isset($data['userId']) ? trim($data['userId']) : '';
        $departmentId = isset($data['departmentId']) ? trim($data['departmentId']) : '';
        $stageId = isset($data['stageId']) ? trim($data['stageId']) : '';

        if (empty($taskId)) {
            return ['success' => false, 'message' => 'ID công việc không được để trống.'];
        }
        if (empty($kanbanTaskId)) {
            return ['success' => false, 'message' => 'ID kanban task không được để trống.'];
        }
        if (empty($userId)) {
            return ['success' => false, 'message' => 'Thông tin người dùng không hợp lệ.'];
        }
        if (empty($departmentId)) {
            return ['success' => false, 'message' => 'Thông tin phòng ban không hợp lệ.'];
        }
        if (empty($stageId)) {
            return ['success' => false, 'message' => 'Thông tin giai đoạn không hợp lệ.'];
        }

        // Kiểm tra đã có assignee chưa
        $checkQuery = "SELECT lv004 
                   FROM da_lh0008 
                   WHERE lv001 = ? AND lv002 = ? AND lv003 = ?";
        $checkStmt = mysqli_prepare($this->db_link, $checkQuery);

        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Lỗi khi kiểm tra trạng thái phân công.'];
        }

        mysqli_stmt_bind_param($checkStmt, "iis", $taskId, $stageId, $departmentId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $existingAssignment = mysqli_fetch_assoc($checkResult);
            mysqli_stmt_close($checkStmt);

            if (
                !empty($existingAssignment['lv004']) &&
                $existingAssignment['lv004'] !== $userId
            ) {
                return ['success' => false, 'message' => 'Công việc này đã được gán cho người khác.'];
            }

            if ($existingAssignment['lv004'] === $userId) {
                return ['success' => false, 'message' => 'Bạn đã được gán công việc này rồi.'];
            }
        } else {
            mysqli_stmt_close($checkStmt);
        }

        // Gán user vào stage
        $assignData = [
            'taskId' => (int)$taskId,
            'newUserId' => $userId,
            'stageId' => (int)$stageId,
            'departmentId' => $departmentId
        ];

        $result = $this->assign_user_to_stage_data($assignData);

        if ($result['success']) {
            // 1. Cập nhật thời gian trong bảng kanban mapping (da_lh0003)
            $updateQuery = "UPDATE da_lh0003 
                        SET lv013 = NOW() 
                        WHERE lv001 = ?";
            $updateStmt = mysqli_prepare($this->db_link, $updateQuery);
            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, "i", $kanbanTaskId);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);
            }

            // -------------------------------------------------------------
            // 2. CẬP NHẬT MỚI: Update userId vào bảng cr_lv0005 (Master Task)
            // -------------------------------------------------------------
            $updateMasterQuery = "UPDATE cr_lv0005 SET lv006 = ? WHERE lv001 = ?";
            $updateMasterStmt = mysqli_prepare($this->db_link, $updateMasterQuery);
            
            if ($updateMasterStmt) {
                // "s" cho userId (string), "i" cho taskId (int)
                mysqli_stmt_bind_param($updateMasterStmt, "si", $userId, $taskId);
                mysqli_stmt_execute($updateMasterStmt);
                mysqli_stmt_close($updateMasterStmt);
            }
            // -------------------------------------------------------------

            return [
                'success' => true,
                'message' => 'Nhận việc thành công.',
                'data' => [
                    'taskId' => $taskId,
                    'kanbanTaskId' => $kanbanTaskId,
                    'assignedTo' => $userId,
                    'stageId' => $stageId,
                    'departmentId' => $departmentId
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Không thể nhận việc.',
                'error' => $result['error'] ?? null
            ];
        }
    }

    /**
     * Phân công user cho một task (ghi vào bảng chính).
     */
    public function assign_user_data($data)
    {
        $taskId = isset($data['taskId']) ? (int)$data['taskId'] : 0;
        $newUserId = isset($data['newUserId']) ? trim($data['newUserId']) : '';

        if ($taskId <= 0 || !isset($data['newUserId'])) {
            return ['success' => false, 'message' => 'Thiếu thông tin công việc hoặc người dùng.'];
        }

        $query = "UPDATE cr_lv0005 SET lv006 = ? WHERE lv001 = ?";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "si", $newUserId, $taskId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật CSDL.', 'error' => $error];
        }
    }

    /**
     * Phân công user cho một công việc tại một giai đoạn cụ thể của phòng ban.
     */
    public function assign_user_to_stage_data($data)
    {
        $taskId = isset($data['taskId']) ? (int)$data['taskId'] : 0;
        $newUserId = isset($data['newUserId']) ? trim($data['newUserId']) : '';
        $stageId = isset($data['stageId']) ? (int)$data['stageId'] : 0;
        $departmentId = isset($data['departmentId']) ? trim($data['departmentId']) : '';

        if ($taskId <= 0 || empty($newUserId) || $stageId <= 0 || empty($departmentId)) {
            return ['success' => false, 'message' => 'Thiếu thông tin để phân công.'];
        }

        // 1. Cập nhật bảng phân công giai đoạn (da_lh0008)
        $query = "REPLACE INTO da_lh0008 (lv001, lv002, lv003, lv004) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "iiss", $taskId, $stageId, $departmentId, $newUserId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            // -----------------------------------------------------------------
            // 2. CẬP NHẬT MỚI: Update userId vào bảng Master (cr_lv0005)
            // Cập nhật cột lv006 = newUserId tại dòng có lv001 = taskId
            // -----------------------------------------------------------------
            $updateMasterQuery = "UPDATE cr_lv0005 SET lv006 = ? WHERE lv001 = ?";
            $updateMasterStmt = mysqli_prepare($this->db_link, $updateMasterQuery);
            
            if ($updateMasterStmt) {
                // "s" cho newUserId (string/varchar), "i" cho taskId (int)
                mysqli_stmt_bind_param($updateMasterStmt, "si", $newUserId, $taskId);
                mysqli_stmt_execute($updateMasterStmt);
                mysqli_stmt_close($updateMasterStmt);
            }
            // -----------------------------------------------------------------

            return ['success' => true];
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật phân công.', 'error' => $error];
        }
    }

    /**
     * Thêm một cột đã tồn tại vào dự án.
     */
    public function add_existing_column_data($data)
    {
        $projectId = isset($data['projectId']) ? $data['projectId'] : '0';
        $stageId = isset($data['stageId']) ? (int)$data['stageId'] : 0;

        if (empty($projectId) || $stageId <= 0) {
            return ['success' => false, 'message' => 'ID dự án và giai đoạn không hợp lệ.'];
        }

        $max_order_query = "SELECT MAX(lv003) as max_order FROM da_lh0005 WHERE lv001 = ?";
        $stmt_max = mysqli_prepare($this->db_link, $max_order_query);
        mysqli_stmt_bind_param($stmt_max, "s", $projectId);
        mysqli_stmt_execute($stmt_max);
        $row_max = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_max));
        $new_order = ($row_max && $row_max['max_order'] ? (int)$row_max['max_order'] : 0) + 1;
        mysqli_stmt_close($stmt_max);

        $query = "INSERT INTO da_lh0005 (lv001, lv002, lv003) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "sii", $projectId, $stageId, $new_order);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi thêm liên kết giai đoạn.', 'error' => $error];
        }
    }

    /**
     * Thiết lập trạng thái đánh giá cho công việc.
     */
    public function set_evaluation_data($data)
    {
        $taskId = isset($data['taskId']) ? (int)$data['taskId'] : 0;
        $status = isset($data['status']) ? trim($data['status']) : 'none';

        if ($taskId <= 0) {
            return ['success' => false, 'message' => 'ID công việc không hợp lệ'];
        }

        $query = "UPDATE da_lh0003 SET lv017 = ? WHERE lv001 = ?";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "si", $status, $taskId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật đánh giá.'];
        }
    }
    // Lấy danh sách icon từ bảng da_lh0010
    public function get_icon_list()
    {
        $result = mysqli_query($this->db_link, "SELECT lv001, lv002 FROM da_lh0010");
        $icons = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $icons[] = [
                'lv001' => $row['lv001'], // mã icon
                'lv002' => $row['lv002'], // tên icon
            ];
        }
        return $icons;
    }

    // Lấy danh sách màu từ bảng da_lh0011
    public function get_color_list()
    {
        $result = mysqli_query($this->db_link, "SELECT lv001, lv002 FROM da_lh0011");
        $colors = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $colors[] = [
                'lv001' => $row['lv001'], // mã màu
                'lv002' => $row['lv002'], // tên màu
            ];
        }
        return $colors;
    }
    // Lấy danh sách icon đã thêm cho dự án từ bảng da_lh0006
    public function get_project_icons($projectId)
    {
        $projectId = isset($projectId) ? $projectId : '0';
        $icons = [];
        $query = "SELECT lv005 AS name, lv006 AS class, lv007 AS color FROM da_lh0006 WHERE lv018 = ?";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $projectId);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $icons[] = [
                    'name' => $row['name'],     // Tên icon
                    'class' => $row['class'],   // Mã class icon
                    'color' => $row['color'],   // Mã màu (text-red-500, ...)
                ];
            }
            mysqli_stmt_close($stmt);
            return $icons;
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi lấy danh sách icon dự án.', 'error' => $error];
        }
    }
    /**
     * Bật/tắt trạng thái hoàn thành, sử dụng ID từ da_lh0003.
     * Đã cập nhật theo yêu cầu mới.
     */
    public function toggle_completion_for_dept_data($data)
    {
        // <<< THAY ĐỔI: Nhận kanbanTaskId làm ID chính cho thao tác này
        $kanbanTaskId = isset($data['kanbanTaskId']) ? (int)$data['kanbanTaskId'] : 0;
        $departmentId = isset($data['departmentId']) ? trim($data['departmentId']) : '';
        $projectId = isset($data['projectId']) ? trim($data['projectId']) : '';
        $isCompleted = isset($data['isCompleted']) ? (int)$data['isCompleted'] : 0;
        $userId = isset($data['userId']) ? trim($data['userId']) : '';
        $isFromInProgress = isset($data['isFromInProgress']) ? (bool)$data['isFromInProgress'] : false; // Thêm flag mới
        $user_role = isset($data['user_role']) ? trim($data['user_role']) : 'user'; // Vai trò người dùng
        // NẾU KHÔNG CÓ PROJECT ID (trường hợp tổng quan phòng ban), TỰ ĐỘNG TÌM
        if (empty($projectId) || $projectId === 'null') {
            $find_project_query = "SELECT lv018 FROM da_lh0003 WHERE lv001 = ? LIMIT 1";
            $stmt_find_project = mysqli_prepare($this->db_link, $find_project_query);
            mysqli_stmt_bind_param($stmt_find_project, "i", $kanbanTaskId);
            mysqli_stmt_execute($stmt_find_project);
            if ($row_project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_project))) {
                $projectId = $row_project['lv018'];
            }
            mysqli_stmt_close($stmt_find_project);
        }

        if ($kanbanTaskId <= 0 || empty($departmentId) || empty($projectId) || empty($userId)) {
            return ['success' => false, 'message' => 'Thiếu thông tin quan trọng (Kanban Task ID, Phòng ban, Dự án, User).'];
        }

        mysqli_autocommit($this->db_link, false);
        $success = true;

        // <<< THAY ĐỔI: Tìm masterTaskId tương ứng để dùng cho các bảng log
        $masterTaskId = null;
        $find_master_id_query = " SELECT t_master.lv001
        FROM cr_lv0005 AS t_master
        JOIN cr_lv0004 AS t_project ON t_project.lv001 = t_master.lv002
        JOIN da_lh0003 AS t_kanban 
            ON t_master.lv501 = t_kanban.lv004
           AND t_kanban.lv018 = t_project.lv501
        WHERE t_kanban.lv001 = ? 
          AND t_project.lv501 = ?
        LIMIT 1";
        $stmt_find_m = mysqli_prepare($this->db_link, $find_master_id_query);
        mysqli_stmt_bind_param($stmt_find_m, "ii", $kanbanTaskId, $projectId);
        mysqli_stmt_execute($stmt_find_m);
        if ($row_m = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_m))) {
            $masterTaskId = $row_m['lv001'];
        }
        mysqli_stmt_close($stmt_find_m);

        if (!$masterTaskId) {
            mysqli_rollback($this->db_link);
            mysqli_autocommit($this->db_link, true);
            return ['success' => false, 'message' => 'Không tìm thấy Master Task ID tương ứng.'];
        }

        // TRƯỜNG HỢP 1: ĐÁNH DẤU "HOÀN THÀNH"
        if ($isCompleted) {
            $current_column_id = null;
            $done_column_id_for_dept = null;

            // XỬ LÝ ĐẶC BIỆT: Nếu đến từ cột "Đang thực hiện", current_column_id = 8
            if ($isFromInProgress) {
                // Đặt current_column_id = 8 (cột đang thực hiện) và tìm cột Done
                $find_cols_query = "SELECT 
                    8 as currentColumnId,
                    (SELECT lv003 FROM da_lh0007 WHERE lv018 = ? AND FIND_IN_SET(?, lv002) > 0 AND lv008 = 1 LIMIT 1) as doneColumnId";
                $stmt_find_cols = mysqli_prepare($this->db_link, $find_cols_query);
                mysqli_stmt_bind_param($stmt_find_cols, "ss", $projectId, $departmentId);
            } else {
                // Logic bình thường: lấy cột hiện tại bất kỳ
                $find_cols_query = "SELECT lv003 as currentColumnId, 
                              (SELECT lv003 FROM da_lh0007 WHERE lv018 = ? AND FIND_IN_SET(?, lv002) > 0 AND lv008 = 1 LIMIT 1) as doneColumnId 
                        FROM da_lh0007 
                        WHERE lv004 = ? AND FIND_IN_SET(?, lv002) > 0 AND lv018 = ? LIMIT 1";
                $stmt_find_cols = mysqli_prepare($this->db_link, $find_cols_query);
                mysqli_stmt_bind_param($stmt_find_cols, "ssiss", $projectId, $departmentId, $kanbanTaskId, $departmentId, $projectId);
            }

            mysqli_stmt_execute($stmt_find_cols);
            if ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_cols))) {
                $current_column_id = $row['currentColumnId'];
                $done_column_id_for_dept = $row['doneColumnId'];
            }
            mysqli_stmt_close($stmt_find_cols);
            $current_assignee_id = null;
            if ($success && $current_column_id) {
                $find_assignee_query = "SELECT lv004 FROM da_lh0008 WHERE lv001 = ? AND lv002 = ? AND lv003 = ? LIMIT 1";
                $stmt_find_assignee = mysqli_prepare($this->db_link, $find_assignee_query);
                // <<< THAY ĐỔI: Dùng $masterTaskId cho lv001
                mysqli_stmt_bind_param($stmt_find_assignee, "iis", $masterTaskId, $current_column_id, $departmentId);
                mysqli_stmt_execute($stmt_find_assignee);
                if ($row_assignee = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_assignee))) {
                    $current_assignee_id = $row_assignee['lv004'];
                }
                mysqli_stmt_close($stmt_find_assignee);
            }

            if ($success && $done_column_id_for_dept) {
                // XỬ LÝ ĐẶC BIỆT: Nếu đến từ cột "Đang thực hiện"
                if (!$isFromInProgress) {
                    // Di chuyển trong view phòng ban (chỉ khi KHÔNG từ cột đang thực hiện)
                    $update_dept_view_query = "UPDATE da_lh0007 SET lv003 = ? WHERE lv004 = ? AND lv002 = ? AND lv018 = ?";
                    $stmt_update_dept = mysqli_prepare($this->db_link, $update_dept_view_query);
                    mysqli_stmt_bind_param($stmt_update_dept, "iiss", $done_column_id_for_dept, $kanbanTaskId, $departmentId, $projectId);
                    if (!mysqli_stmt_execute($stmt_update_dept)) $success = false;
                    mysqli_stmt_close($stmt_update_dept);
                } else {
                    // Nếu từ cột "Đang thực hiện": Cập nhật record cột 8 thành cột Done
                    // Cập nhật lv003 (8 -> 7) nhưng GIỮ NGUYÊN lv008 = 2 để lưu trạng thái đã qua "Đang thực hiện"
                    $update_in_progress_to_done_query = "UPDATE da_lh0007 SET lv003 = ? WHERE lv004 = ? AND lv003 = 8 AND FIND_IN_SET(?, lv002) > 0 AND lv018 = ?";
                    $stmt_update_to_done = mysqli_prepare($this->db_link, $update_in_progress_to_done_query);
                    mysqli_stmt_bind_param($stmt_update_to_done, "iiss", $done_column_id_for_dept, $kanbanTaskId, $departmentId, $projectId);
                    if (!mysqli_stmt_execute($stmt_update_to_done)) $success = false;
                    mysqli_stmt_close($stmt_update_to_done);
                }

                // --- B4: GÁN LẠI người đảm nhận cũ vào công việc ở cột DONE mới --- CHỈ NẾU KHÔNG PHẢI ADMIN
                if ($success) {
                    $assignee_for_done = null;

                    if (!empty($current_assignee_id)) {
                        $assignee_for_done = $current_assignee_id;
                    } elseif ($user_role !== 'admin' && $user_role !=='manager') {
                        $assignee_for_done = $userId;
                    }

                    // Chỉ thực hiện gán nếu xác định được người ($assignee_for_done không null)
                    if ($assignee_for_done) {
                        $assign_query = "REPLACE INTO da_lh0008 (lv001, lv002, lv003, lv004) VALUES (?, ?, ?, ?)";
                        $stmt_assign = mysqli_prepare($this->db_link, $assign_query);
                        // Dùng $masterTaskId cho lv001
                        mysqli_stmt_bind_param($stmt_assign, "iiss", $masterTaskId, $done_column_id_for_dept, $departmentId, $assignee_for_done);
                        if (!mysqli_stmt_execute($stmt_assign)) $success = false;
                        mysqli_stmt_close($stmt_assign);
                    }
                }
            }
            $assignee_for_log = $current_assignee_id ? $current_assignee_id : $userId;
            $query_completion = "REPLACE INTO da_lh0009 (lv001, lv002, lv003, lv004, lv005, lv006) VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmt_completion = mysqli_prepare($this->db_link, $query_completion);
            mysqli_stmt_bind_param($stmt_completion, "isiis", $masterTaskId, $departmentId, $current_column_id, $isCompleted, $assignee_for_log);
            if (!mysqli_stmt_execute($stmt_completion)) $success = false;
            mysqli_stmt_close($stmt_completion);


            // --- B6: Kiểm tra toàn bộ dự án và di chuyển task chính nếu cần ---
            if ($success) {
                $total_assigned = 0;
                $query_assigned = "SELECT COUNT(DISTINCT lv002) as total FROM da_lh0007 WHERE lv004 = ? AND lv018 = ?";
                $stmt_assigned = mysqli_prepare($this->db_link, $query_assigned);
                // <<< THAY ĐỔI: Dùng $kanbanTaskId cho lv004
                mysqli_stmt_bind_param($stmt_assigned, "is", $kanbanTaskId, $projectId);
                mysqli_stmt_execute($stmt_assigned);
                if ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_assigned))) $total_assigned = (int)$row['total'];
                mysqli_stmt_close($stmt_assigned);

                $total_completed = 0;
                $query_completed = "SELECT COUNT(*) as total FROM da_lh0009 WHERE lv001 = ? AND lv004 = 1";
                $stmt_completed = mysqli_prepare($this->db_link, $query_completed);
                // <<< THAY ĐỔI: Dùng $masterTaskId cho lv001
                mysqli_stmt_bind_param($stmt_completed, "i", $masterTaskId);
                mysqli_stmt_execute($stmt_completed);
                if ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_completed))) $total_completed = (int)$row['total'];
                mysqli_stmt_close($stmt_completed);

                if ($total_assigned > 0 && $total_assigned === $total_completed) {
                    $project_done_column_id = null;
                    // Lấy cột done chung của dự án
                    $find_done_query = "SELECT t_stage.lv001 FROM da_lh0004 AS t_stage JOIN da_lh0005 AS t_proj_map ON t_stage.lv001 = t_proj_map.lv002 WHERE t_proj_map.lv001 = ? AND t_stage.lv002 LIKE '%DONE%' LIMIT 1";
                    $stmt_find_done = mysqli_prepare($this->db_link, $find_done_query);
                    mysqli_stmt_bind_param($stmt_find_done, "s", $projectId);
                    mysqli_stmt_execute($stmt_find_done);
                    if ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_done))) $project_done_column_id = $row['lv001'];
                    mysqli_stmt_close($stmt_find_done);

                    if ($project_done_column_id) {
                        $update_main_task_query = "UPDATE da_lh0003 SET lv003 = ? WHERE lv001 = ? AND lv018 = ?";
                        $stmt_update_main = mysqli_prepare($this->db_link, $update_main_task_query);
                        // <<< THAY ĐỔI: Dùng $kanbanTaskId để xác định dòng trong da_lh0003
                        mysqli_stmt_bind_param($stmt_update_main, "iis", $project_done_column_id, $kanbanTaskId, $projectId);
                        if (!mysqli_stmt_execute($stmt_update_main)) $success = false;
                        mysqli_stmt_close($stmt_update_main);
                    }
                }
            }
        } else { // TRƯỜNG HỢP 2: HỦY "HOÀN THÀNH"
            $previous_stage_id = null;
            $find_prev_query = "SELECT lv003 FROM da_lh0009 WHERE lv001 = ? AND lv002 = ? LIMIT 1";
            $stmt_find_prev = mysqli_prepare($this->db_link, $find_prev_query);
            mysqli_stmt_bind_param($stmt_find_prev, "is", $masterTaskId, $departmentId);
            mysqli_stmt_execute($stmt_find_prev);
            if ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_find_prev))) {
                $previous_stage_id = $row['lv003'];
            }
            mysqli_stmt_close($stmt_find_prev);

            // Kiểm tra xem có record từ cột "Đang thực hiện" không (bao gồm cả record đã bị chuyển sang Done)
            $has_in_progress_record = false;
            $in_progress_record_id = null;
            $check_in_progress_query = "SELECT lv001 FROM da_lh0007 WHERE lv004 = ? AND FIND_IN_SET(?, lv002) > 0 AND lv018 = ? AND (lv003 = 8 OR lv008 = 2) LIMIT 1";
            $stmt_check_ip = mysqli_prepare($this->db_link, $check_in_progress_query);
            mysqli_stmt_bind_param($stmt_check_ip, "iss", $kanbanTaskId, $departmentId, $projectId);
            mysqli_stmt_execute($stmt_check_ip);
            if ($row_ip = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check_ip))) {
                $has_in_progress_record = true;
                $in_progress_record_id = $row_ip['lv001'];
            }
            mysqli_stmt_close($stmt_check_ip);

            if ($success && $previous_stage_id) {
                if ($has_in_progress_record && $previous_stage_id == 8) {
                    // Nếu previous_stage_id = 8 (từ cột đang thực hiện), XÓA LUÔN record có lv003 = 7 và lv008 = 2
                    $delete_done_record_query = "DELETE FROM da_lh0007 WHERE lv004 = ? AND lv003 = 7 AND lv008 = 2 AND FIND_IN_SET(?, lv002) > 0 AND lv018 = ?";
                    $stmt_delete_done = mysqli_prepare($this->db_link, $delete_done_record_query);
                    mysqli_stmt_bind_param($stmt_delete_done, "iss", $kanbanTaskId, $departmentId, $projectId);
                    if (!mysqli_stmt_execute($stmt_delete_done)) $success = false;
                    mysqli_stmt_close($stmt_delete_done);

                    // Sau khi xóa record Done, cập nhật lại record cột "Đang thực hiện" (lv003 = 8, lv008 = 2)
                    $reactivate_in_progress_query = "UPDATE da_lh0007 SET lv003 = 8, lv008 = 2 WHERE lv001 = ?";
                    $stmt_reactivate = mysqli_prepare($this->db_link, $reactivate_in_progress_query);
                    mysqli_stmt_bind_param($stmt_reactivate, "i", $in_progress_record_id);
                    if (!mysqli_stmt_execute($stmt_reactivate)) $success = false;
                    mysqli_stmt_close($stmt_reactivate);
                } else {
                    // Trường hợp bình thường: trả về cột cũ
                    $update_dept_view_query = "UPDATE da_lh0007 SET lv003 = ? WHERE lv004 = ? AND lv002 = ? AND lv018 = ?";
                    $stmt_update = mysqli_prepare($this->db_link, $update_dept_view_query);
                    mysqli_stmt_bind_param($stmt_update, "iiss", $previous_stage_id, $kanbanTaskId, $departmentId, $projectId);
                    if (!mysqli_stmt_execute($stmt_update)) $success = false;
                    mysqli_stmt_close($stmt_update);
                }
            }

            // Cập nhật log (dùng masterTaskId)
            $query_uncompletion = "UPDATE da_lh0009 SET lv004 = 0, lv005 = NULL, lv006 = ? WHERE lv001 = ? AND lv002 = ?";
            $stmt_uncompletion = mysqli_prepare($this->db_link, $query_uncompletion);
            mysqli_stmt_bind_param($stmt_uncompletion, "sis", $userId, $masterTaskId, $departmentId);
            if (!mysqli_stmt_execute($stmt_uncompletion)) $success = false;
            mysqli_stmt_close($stmt_uncompletion);

            if ($success && $previous_stage_id) {
                $update_main_task_query = "UPDATE da_lh0003 SET lv003 = ? WHERE lv001 = ? AND lv018 = ?";
                $stmt_update_main = mysqli_prepare($this->db_link, $update_main_task_query);
                // <<< THAY ĐỔI: Dùng $kanbanTaskId
                mysqli_stmt_bind_param($stmt_update_main, "iis", $previous_stage_id, $kanbanTaskId, $projectId);
                if (!mysqli_stmt_execute($stmt_update_main)) $success = false;
                mysqli_stmt_close($stmt_update_main);
            }
        }

        // --- COMMIT HOẶC ROLLBACK ---
        if ($success) {
            mysqli_commit($this->db_link);
            $response = ['success' => true];
        } else {
            mysqli_rollback($this->db_link);
            $response = ['success' => false, 'message' => 'Lỗi khi cập nhật CSDL.'];
        }
        mysqli_autocommit($this->db_link, true);
        return $response;
    }

    // ===================================================================
    // ==         CÁC HÀM LẤY DỮ LIỆU PHỤ (USERS, FILTERS, ETC.)        ==
    // ===================================================================

    /**
     * Lấy danh sách các cột chưa được gán cho dự án.
     */
    public function get_available_columns_data($projectId)
    {
        if (empty($projectId)) {
            return [];
        }

        $query = "SELECT lv001 as id, lv002 as title 
                  FROM da_lh0004 
                  WHERE lv001 NOT IN (SELECT lv002 FROM da_lh0005 WHERE lv001 = ?)";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $projectId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $columns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $columns;
    }

    /**
     * Lấy bình luận của một công việc.
     */
    public function get_comments_data($taskId)
    {
        if ($taskId <= 0) {
            return [];
        }
        $query = "SELECT c.comment_text, c.created_at, u.lv002 as userName 
                  FROM da_task_comments c
                  JOIN hr_lv0020 u ON c.user_id = u.lv001
                  WHERE c.task_id = ?
                  ORDER BY c.created_at ASC";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "i", $taskId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $comments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $comments[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $comments;
    }

    /**
     * Lấy danh sách cho các bộ lọc.
     */
    public function get_filters_data()
    {
        $departments = $this->get_filter_options('hr_lv0002', 'lv001', 'lv003');
        $projects = $this->get_filter_options('cr_lv0004', 'lv501', 'lv002');
        return ['departments' => $departments, 'projects' => $projects];
    }

    /**
     * Lấy thông tin người dùng đang đăng nhập.
     */
    public function get_current_user_data($userId)
    {
        if (empty($userId)) {
            return null;
        }

        $user_role = null;

        // --- BƯỚC 1: Truy vấn bảng quyền (lv_lv0007) TRƯỚC ---
        $role_query = "SELECT lv003 FROM lv_lv0007 WHERE lv006 = ? LIMIT 1";
        $stmt_role = mysqli_prepare($this->db_link, $role_query);

        if ($stmt_role) {
            mysqli_stmt_bind_param($stmt_role, "s", $userId);
            mysqli_stmt_execute($stmt_role);
            $role_result = mysqli_stmt_get_result($stmt_role);
            if ($role_row = mysqli_fetch_assoc($role_result)) {
                $user_role = $role_row['lv003']; // Lấy quyền, ví dụ: 'admin', 'manager'
            }
            mysqli_stmt_close($stmt_role);
        }

        // --- BƯỚC 2: Lấy thông tin user từ bảng hr_lv0020 ---
        $query = "SELECT t1.lv001 as id, t1.lv002 as name, t1.lv029 as departmentId, t2.lv003 as departmentName
              FROM hr_lv0020 as t1
              LEFT JOIN hr_lv0002 as t2 ON t1.lv029 = t2.lv001
              WHERE t1.lv001 = ? LIMIT 1";

        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $user = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $user = $row;
            // ✅ Luôn thêm trường 'role' vào kết quả (nhất quán cho tất cả user)
            $user['role'] = $user_role ? $user_role : 'user';
        }
        mysqli_stmt_close($stmt);
        
        return $user;
    }

    /**
     * Lấy danh sách các dự án mà một phòng ban cụ thể được phân công.
     */
    public function get_projects_by_department_data($departmentId)
    {
        if (empty($departmentId)) {
            return [];
        }

        $projects = [];
        $query = "SELECT DISTINCT t_project.lv501 as id, t_project.lv002 as name
                FROM da_lh0007 AS t_mapping
                JOIN cr_lv0004 AS t_project ON t_mapping.lv018 = t_project.lv501
                WHERE 
                    FIND_IN_SET(?, t_mapping.lv002) > 0 -- <<< THAY ĐỔI Ở ĐÂY
                    AND t_mapping.lv004 IS NOT NULL AND t_mapping.lv004 != ''";

        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $departmentId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $projects[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $projects;
    }

    // ===================================================================
    // ==                        CÁC HÀM HỖ TRỢ                         ==
    // ===================================================================

    /**
     * Hàm helper để lấy các lựa chọn cho bộ lọc.
     */
    private function get_filter_options($tableName, $idCol, $nameCol)
    {
        $options = [];
        $query = "SELECT $idCol as id, $nameCol as name FROM $tableName ORDER BY $nameCol ASC";
        $result = db_query($query);
        while ($row = mysqli_fetch_assoc($result)) {
            $options[] = $row;
        }
        return $options;
    }

    /**
     * Hàm helper để lấy ký tự đầu của tên.
     */
    private function getInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $w) {
            $initials .= mb_substr($w, 0, 1);
        }
        return mb_strtoupper($initials);
    }
    public function get_work_log_data_for_timeline($taskId)
    {
        if (empty($taskId)) {
            return [];
        }

        $query = "SELECT lv005 AS execution_datetime, lv004 AS work_content, lv008 AS user_id 
                FROM cr_lv0090 
                WHERE lv002 = ?
                ORDER BY lv005 ASC";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_bind_param($stmt, "s", $taskId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $worklogs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $worklogs[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $worklogs;
    }
    public function get_work_log_data($taskId, $userId)
    {
        if (empty($taskId) || empty($userId)) {
            return [];
        }

        $logs = [];

        // Lấy role của user
        $roleQuery = "SELECT lv003 FROM lv_lv0007 WHERE lv006 = ?";
        $roleStmt = mysqli_prepare($this->db_link, $roleQuery);
        mysqli_stmt_bind_param($roleStmt, "s", $userId);
        mysqli_stmt_execute($roleStmt);
        $roleResult = mysqli_stmt_get_result($roleStmt);
        $userRole = '';
        if ($row = mysqli_fetch_assoc($roleResult)) {
            $userRole = strtolower(trim($row['lv003']));
        }
        mysqli_stmt_close($roleStmt);

        // Nếu là admin hoặc pm => thấy tất cả worklog
        if ($userRole === 'admin' || $userRole === 'pm') {
            $query = "SELECT 
                    log.lv001 as id,
                    log.lv004 as work_content,
                    log.lv005 as execution_datetime,
                    log.lv015 as creation_date,
                    usr.lv004 as user_name
                  FROM 
                    cr_lv0090 AS log
                  JOIN 
                    lv_lv0007 AS usr ON log.lv008 = usr.lv006
                  WHERE 
                    log.lv002 = ?
                  ORDER BY 
                    log.lv005 DESC";

            $stmt = mysqli_prepare($this->db_link, $query);
            mysqli_stmt_bind_param($stmt, "s", $taskId);
        } else {
            // User thường => chỉ thấy worklog của chính mình
            $query = "SELECT 
                    log.lv001 as id,
                    log.lv004 as work_content,
                    log.lv005 as execution_datetime,
                    log.lv015 as creation_date,
                    usr.lv004 as user_name
                  FROM 
                    cr_lv0090 AS log
                  JOIN 
                    lv_lv0007 AS usr ON log.lv008 = usr.lv006
                  WHERE 
                    log.lv002 = ? AND log.lv008 = ?
                  ORDER BY 
                    log.lv005 DESC";

            $stmt = mysqli_prepare($this->db_link, $query);
            mysqli_stmt_bind_param($stmt, "ss", $taskId, $userId);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $logs;
    }

    public function create_work_log_data($data)
    {
        // Lấy dữ liệu từ frontend
        $taskId = isset($data['taskId']) ? trim($data['taskId']) : '';
        $workContent = isset($data['workContent']) ? trim($data['workContent']) : '';
        $executionDateTime = isset($data['executionDateTime']) ? trim($data['executionDateTime']) : date('Y-m-d H:i:s');
        $userId = isset($data['userId']) ? trim($data['userId']) : '';

        if (empty($taskId) || empty($workContent) || empty($userId)) {
            return ['success' => false, 'message' => 'Thiếu thông tin Task ID, nội dung công việc hoặc người thực hiện.'];
        }

        // Ngày tạo record
        $creationDate = date('Y-m-d');

        // Chuẩn bị câu lệnh SQL
        $query = "INSERT INTO cr_lv0090 (lv002, lv004, lv005, lv015,lv008) VALUES (?, ?, ?, NOW(),?)";

        $stmt = mysqli_prepare($this->db_link, $query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi khi chuẩn bị câu lệnh SQL.'];
        }

        // Gắn tham số (bind parameters)
        mysqli_stmt_bind_param($stmt, "isss", $taskId, $workContent, $executionDateTime, $userId);

        // Thực thi và trả về kết quả
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true, 'message' => 'Ghi nhận công việc thành công.'];
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Lỗi khi ghi vào cơ sở dữ liệu.', 'error' => $error];
        }
    }

    public function get_project_status_list()
    {
        // Lấy danh sách trạng thái dự án từ bảng cr_lv0093
        $query = "SELECT lv001 AS id, lv002 AS name FROM cr_lv0093 ORDER BY lv001 ASC";
        $stmt = mysqli_prepare($this->db_link, $query);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $statuses = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $statuses[] = $row;
        }
        mysqli_stmt_close($stmt);

        return [
            'success' => true,
            'data' => $statuses
        ];
    }

        public function get_department_overview_data($departmentId, $userId, $user_role)
        {
            if (empty($departmentId)) {
                return ['success' => false, 'message' => 'Thiếu ID Phòng ban.'];
            }

            $users = [];

            // Bước 1: Lấy tất cả nhân viên của phòng ban
            $users_query = "SELECT 
                            emp.lv001 AS id, 
                            emp.lv002 AS name 
                        FROM 
                            hr_lv0020 AS emp
                        WHERE 
                            emp.lv029 = ?";

            $stmt_users = mysqli_prepare($this->db_link, $users_query);
            if ($stmt_users) {
                mysqli_stmt_bind_param($stmt_users, "s", $departmentId);
                mysqli_stmt_execute($stmt_users);
                $users_result = mysqli_stmt_get_result($stmt_users);

                $colors = ['luxury-gradient', 'luxury-gradient-2', 'luxury-gradient-3', 'luxury-gradient-4', 'luxury-gradient-5'];
                $color_index = 0;
                while ($user_row = mysqli_fetch_assoc($users_result)) {
                    $user_row['initials'] = $this->getInitials($user_row['name']);
                    $user_row['color'] = $colors[$color_index % count($colors)];
                    $users[] = $user_row;
                    $color_index++;
                }
                mysqli_stmt_close($stmt_users);
            }

            $columns = [];
            $column_ids = [];

            // Lấy tất cả cột của phòng ban (từ tất cả dự án)
            // Loại trừ cột Done (id = 7) khỏi query chính để xử lý riêng
            $columns_query = "SELECT DISTINCT t_col.lv002 AS id, t_stage.lv002 AS title, COALESCE(t_dept_map.lv008, 0) AS is_done_column 
                  FROM da_lh0005 AS t_col 
                  JOIN da_lh0004 AS t_stage ON t_col.lv002 = t_stage.lv001 
                  LEFT JOIN da_lh0007 AS t_dept_map ON t_col.lv002 = t_dept_map.lv003 
                  WHERE t_col.lv004 = ? AND t_col.lv002 != 7 
                  ORDER BY t_col.lv003 ASC";

            $stmt_cols = mysqli_prepare($this->db_link, $columns_query);
            mysqli_stmt_bind_param($stmt_cols, "s", $departmentId);
            mysqli_stmt_execute($stmt_cols);
            $columns_result = mysqli_stmt_get_result($stmt_cols);
            while ($row = mysqli_fetch_assoc($columns_result)) {
                if (!in_array($row['id'], $column_ids)) {
                    $columns[] = [
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'tasks' => [],
                        'is_done_column' => $row['is_done_column']
                    ];
                    $column_ids[] = $row['id'];
                }
            }
            mysqli_stmt_close($stmt_cols);

            // Luôn thêm cột DONE (id = 7) vào cuối nếu có
            $done_column_query = "SELECT DISTINCT t_stage.lv001 AS id, t_stage.lv002 AS title
                            FROM da_lh0004 AS t_stage
                            JOIN da_lh0007 AS t_dept_map ON t_stage.lv001 = t_dept_map.lv003
                            WHERE FIND_IN_SET(?, t_dept_map.lv002) > 0 
                            AND t_stage.lv001 = 7 LIMIT 1";

            $stmt_done = mysqli_prepare($this->db_link, $done_column_query);
            mysqli_stmt_bind_param($stmt_done, "s", $departmentId);
            mysqli_stmt_execute($stmt_done);
            $done_result = mysqli_stmt_get_result($stmt_done);
            if ($done_row = mysqli_fetch_assoc($done_result)) {
                if (!in_array($done_row['id'], $column_ids)) {
                    $columns[] = ['id' => $done_row['id'], 'title' => $done_row['title'], 'tasks' => [], 'is_done_column' => 1];
                }
            }
            mysqli_stmt_close($stmt_done);

            $tasks = [];

            // TRUY VẤN 1: Lấy các công việc ĐANG HOẠT ĐỘNG (từ tất cả dự án của phòng ban)
           $active_tasks_query = "
            SELECT 
                t_kanban.lv001 AS kanbanTaskId, 
                t_master.lv001 AS id, 
                t_kanban.lv004 AS taskId, 
                t_kanban.lv005 AS title, 
                t_kanban.lv007 AS description,
                t_project.lv002 AS projectName,
                t_kanban.lv013 AS startDate,
                t_kanban.lv019 AS endDate,
                -- Lấy ID cột (lv002) từ bảng da_lh0005
                t_column.lv002 AS columnId, 
                
                -- Tính toán Assignee ID
                COALESCE(
                    (SELECT sa.lv004 
                     FROM da_lh0008 sa 
                     WHERE sa.lv001 = t_master.lv001 
                     AND sa.lv002 = t_mapping.lv003 
                     AND FIND_IN_SET(?, sa.lv003) -- Param 1: departmentId
                     LIMIT 1),
                    t_kanban.lv016
                ) AS assigneeId,

                -- Lấy Assignee Name
                assignee_user.lv002 AS assigneeName,
                
                0 AS is_completed, 
                t_kanban.lv017 AS evaluation_status,
                eval_icon.lv006 AS evaluation_icon, 
                eval_icon.lv007 AS evaluation_color

            FROM cr_lv0005 AS t_master

            -- Vẫn Join Project để lấy tên dự án (projectName) hiển thị cho đẹp
            JOIN cr_lv0004 AS t_project 
                ON t_project.lv001 = t_master.lv002 

            JOIN da_lh0003 AS t_kanban 
                ON t_master.lv501 = t_kanban.lv004
                AND t_kanban.lv018 = t_project.lv501 

            JOIN da_lh0007 AS t_mapping 
                ON t_kanban.lv001 = t_mapping.lv004

            -- ✅ [FIX] JOIN BẢNG CỘT MASTER (da_lh0005)
            -- CHỈ Map ID cột (lv002), KHÔNG Map Project ID nữa
            JOIN da_lh0005 AS t_column
                ON t_mapping.lv003 = t_column.lv002 
            
            LEFT JOIN da_lh0006 AS eval_icon 
                ON t_kanban.lv017 = eval_icon.lv005 
                AND (eval_icon.lv018 = t_kanban.lv018 OR eval_icon.lv018 = '0')

            LEFT JOIN hr_lv0020 AS assignee_user 
                ON assignee_user.lv001 = COALESCE(
                    (SELECT sa.lv004 
                     FROM da_lh0008 sa 
                     WHERE sa.lv001 = t_master.lv001 
                     AND sa.lv002 = t_mapping.lv003 
                     AND FIND_IN_SET(?, sa.lv003) -- Param 2: departmentId
                     LIMIT 1),
                    t_kanban.lv016
                )

            WHERE 
                FIND_IN_SET(?, t_mapping.lv002) > 0 -- Param 3: departmentId
                
                -- ❌ ĐÃ XÓA: AND t_kanban.lv018 = ? (Không lọc theo Project ID nữa)
                
                -- LOGIC 1: Lọc GIAI ĐOẠN đầu tiên chưa hoàn thành
                AND t_mapping.lv003 = ( 
                    SELECT MIN(s1.lv003) 
                    FROM da_lh0007 s1
                    WHERE s1.lv004 = t_kanban.lv001
                    AND ( (LENGTH(s1.lv002) - LENGTH(REPLACE(s1.lv002, ',', '')) + 1) 
                          != 
                          ( SELECT COUNT(DISTINCT tc.lv002)
                            FROM da_lh0009 tc
                            WHERE tc.lv001 = t_master.lv001
                                AND tc.lv004 = 1
                                AND FIND_IN_SET(tc.lv002, s1.lv002) > 0
                          )
                        )
                )
                
                -- LOGIC 2: Lọc ƯU TIÊN đầu tiên
                AND t_mapping.lv009 = (
                    SELECT MIN(s2.lv009)
                    FROM da_lh0007 s2
                    WHERE s2.lv004 = t_kanban.lv001
                      AND s2.lv003 = t_mapping.lv003 
                      AND ( (LENGTH(s2.lv002) - LENGTH(REPLACE(s2.lv002, ',', '')) + 1) 
                            != 
                            (
                                SELECT COUNT(DISTINCT tc.lv002)
                                FROM da_lh0009 tc
                                WHERE tc.lv001 = t_master.lv001
                                    AND tc.lv004 = 1
                                    AND FIND_IN_SET(tc.lv002, s2.lv002) > 0
                            )
                          )
                )
        ";

        // ✅ Cập nhật tham số Bind: CHỈ CÒN 3 tham số departmentId (Bỏ projectId)
        $params = [$departmentId, $departmentId, $departmentId]; 
        $types = "sss";

            // ✅ Lọc theo user nếu không phải admin hoặc manager
            if ($user_role !== 'admin' && $user_role !== 'manager') {
                $active_tasks_query .= " HAVING (assigneeId = ? OR assigneeId IS NULL OR assigneeId = '')";
                $params[] = $userId;
                $types .= "s";
            }

            $active_tasks_query .= " ORDER BY t_mapping.lv009 ASC";

            $stmt_active = mysqli_prepare($this->db_link, $active_tasks_query);
            mysqli_stmt_bind_param($stmt_active, $types, ...$params);
            mysqli_stmt_execute($stmt_active);
            $active_result = mysqli_stmt_get_result($stmt_active);
            while ($task_row = mysqli_fetch_assoc($active_result)) {
                $tasks[$task_row['id']] = $task_row;
            }
            mysqli_stmt_close($stmt_active);

            // TRUY VẤN 2: Lấy các công việc ĐÃ HOÀN THÀNH (từ tất cả dự án của phòng ban)
            $completed_tasks_query = "
            SELECT 
                    t_kanban.lv001 AS kanbanTaskId, 
                    t_master.lv001 AS id, 
                    t_kanban.lv004 AS taskId, 
                    t_kanban.lv005 AS title, 
                    t_kanban.lv007 AS description,
                    t_kanban.lv013 AS startDate,
                    t_kanban.lv019 AS endDate,
                    t_project.lv002 AS projectName,
                    (
                        SELECT lv003 
                        FROM da_lh0007 
                        WHERE FIND_IN_SET(?, lv002) > 0 
                        AND lv008 = 1 
                        LIMIT 1
                    ) AS columnId,
                    COALESCE(
                        (
                            SELECT sa.lv004 
                            FROM da_lh0008 sa
                            WHERE sa.lv001 = t_master.lv001 
                            AND sa.lv003 = t_completion.lv002
                            AND sa.lv002 = (
                                SELECT m.lv003 
                                FROM da_lh0007 m 
                                WHERE FIND_IN_SET(t_completion.lv002, m.lv002) > 0
                                    AND m.lv008 = 1
                                LIMIT 1
                            )
                            LIMIT 1
                        ),
                        t_kanban.lv016
                    ) AS assigneeId,
                    (SELECT ui.lv002 FROM hr_lv0020 ui WHERE ui.lv001 = assigneeId) AS assigneeName,
                    1 AS is_completed, 
                    t_kanban.lv017 AS evaluation_status,
                    eval_icon.lv006 AS evaluation_icon, 
                    eval_icon.lv007 AS evaluation_color
                FROM cr_lv0005 AS t_master
                JOIN cr_lv0004 AS t_project 
                    ON t_project.lv001 = t_master.lv002 
                JOIN da_lh0003 AS t_kanban 
                    ON t_master.lv501 = t_kanban.lv004
                AND t_kanban.lv018 = t_project.lv501 
                JOIN da_lh0009 AS t_completion 
                    ON t_master.lv001 = t_completion.lv001
                LEFT JOIN da_lh0006 AS eval_icon 
                    ON t_kanban.lv017 = eval_icon.lv005 
                AND (eval_icon.lv018 = t_kanban.lv018 OR eval_icon.lv018 = '0')
                WHERE 
                    FIND_IN_SET(?, t_completion.lv002) > 0 
                    AND t_completion.lv004 = 1;
            ";

            $stmt_completed = mysqli_prepare($this->db_link, $completed_tasks_query);
            mysqli_stmt_bind_param($stmt_completed, "ss", $departmentId, $departmentId);
            mysqli_stmt_execute($stmt_completed);
            $completed_result = mysqli_stmt_get_result($stmt_completed);
            while ($task_row = mysqli_fetch_assoc($completed_result)) {
                $tasks[$task_row['id']] = $task_row;
            }
            mysqli_stmt_close($stmt_completed);

            // TRUY VẤN 3: Lấy các công việc ở trạng thái "ĐANG THỰC HIỆN" (lv008=2)
            $in_progress_tasks_query = "
            SELECT 
                    t_kanban.lv001 AS kanbanTaskId, 
                    t_master.lv001 AS id, 
                    t_kanban.lv004 AS taskId, 
                    t_kanban.lv005 AS title, 
                    t_kanban.lv007 AS description,
                    t_mapping.lv003 AS columnId,
                    t_kanban.lv013 AS startDate,
                    t_kanban.lv019 AS endDate,
                    t_project.lv002 AS projectName,
                    COALESCE(
                        (SELECT sa.lv004 
                        FROM da_lh0008 sa 
                        WHERE sa.lv001 = t_master.lv001 
                        AND sa.lv002 = t_mapping.lv003 
                        AND FIND_IN_SET(?, sa.lv003) 
                        LIMIT 1),
                        t_kanban.lv016
                    ) AS assigneeId,
                    (SELECT ui.lv002 FROM hr_lv0020 ui WHERE ui.lv001 = assigneeId) AS assigneeName,
                    0 AS is_completed, 
                    t_kanban.lv017 AS evaluation_status,
                    eval_icon.lv006 AS evaluation_icon, 
                    eval_icon.lv007 AS evaluation_color
                FROM cr_lv0005 AS t_master
                JOIN cr_lv0004 AS t_project 
                    ON t_project.lv001 = t_master.lv002 
                JOIN da_lh0003 AS t_kanban 
                    ON t_master.lv501 = t_kanban.lv004
                AND t_kanban.lv018 = t_project.lv501 
                JOIN da_lh0007 AS t_mapping 
                    ON t_kanban.lv001 = t_mapping.lv004
                LEFT JOIN da_lh0006 AS eval_icon 
                    ON t_kanban.lv017 = eval_icon.lv005 
                AND (eval_icon.lv018 = t_kanban.lv018 OR eval_icon.lv018 = '0')
                LEFT JOIN da_lh0009 AS t_completion 
                    ON t_master.lv001 = t_completion.lv001
                    AND FIND_IN_SET(?, t_completion.lv002)
                WHERE 
                    FIND_IN_SET(?, t_mapping.lv002) > 0 
                    AND t_mapping.lv008 = 2
                    AND (t_completion.lv004 IS NULL OR t_completion.lv004 != 1)
            ";

            $in_progress_params = [$departmentId, $departmentId, $departmentId];
            $in_progress_types = "sss";

            // ✅ Áp dụng cùng logic lọc user như truy vấn active tasks - manager và admin xem tất cả
            if ($user_role !== 'admin' && $user_role !== 'manager') {
                $in_progress_tasks_query .= " HAVING (assigneeId = ? OR assigneeId IS NULL OR assigneeId = '')";
                $in_progress_params[] = $userId;
                $in_progress_types .= "s";
            }

            $in_progress_tasks_query .= " ORDER BY t_mapping.lv009 ASC";

            $stmt_in_progress = mysqli_prepare($this->db_link, $in_progress_tasks_query);
            mysqli_stmt_bind_param($stmt_in_progress, $in_progress_types, ...$in_progress_params);
            mysqli_stmt_execute($stmt_in_progress);
            $in_progress_result = mysqli_stmt_get_result($stmt_in_progress);
            while ($task_row = mysqli_fetch_assoc($in_progress_result)) {
                $tasks[$task_row['id']] = $task_row;
            }
            mysqli_stmt_close($stmt_in_progress);

            // Chuyển mảng kết hợp về mảng tuần tự
            $final_tasks = array_values($tasks);

            // Lấy evaluation icons
            $eval_icons = [];
            $icons_query = "SELECT lv005 as status, lv005 as text, lv006 as icon, lv007 as color FROM da_lh0006 WHERE lv018 = '0'";
            $stmt_icons = mysqli_prepare($this->db_link, $icons_query);
            mysqli_stmt_execute($stmt_icons);
            $icons_result = mysqli_stmt_get_result($stmt_icons);
            while ($icon_row = mysqli_fetch_assoc($icons_result)) {
                $eval_icons[] = $icon_row;
            }
            mysqli_stmt_close($stmt_icons);

            // Gán tasks vào columns
            foreach ($final_tasks as $task) {
                foreach ($columns as &$column) {
                    if ($column['id'] == $task['columnId']) {
                        $column['tasks'][] = $task;
                        break;
                    }
                }
            }

            return [
                'columns' => $columns,
                'users' => $users,
                'evaluation_icons' => $eval_icons,
                'is_department_overview' => true // ✅ Đánh dấu đây là chế độ tổng quan phòng ban
            ];
        }
}