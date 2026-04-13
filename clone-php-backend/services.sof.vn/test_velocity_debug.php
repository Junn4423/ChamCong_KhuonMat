<?php
// Test script để debug velocity chart

// Test 1: Kiểm tra logic tính start_date
$task_details_sample = [
  '183' => [
    'task_code' => 'ITEM',
    'task_name' => '',
    'start_date' => '2025-11-11',
    'end_date' => '2025-11-19'
  ],
  '184' => [
    'task_code' => 'CV001',
    'task_name' => '',
    'start_date' => '2025-11-10',
    'end_date' => '2025-11-26'
  ]
];

echo "=== TEST LOGIC TÍNH START_DATE ===\n";

// Logic hiện tại
$actual_start_dates = [];
foreach ($task_details_sample as $detail) {
  if (!empty($detail['start_date']) && $detail['start_date'] != '0000-00-00') {
    $actual_start_dates[] = $detail['start_date'];
  }
}

$min_start_date = null; // Giả sử từ query bị null
$start_date = !empty($actual_start_dates) ? min($actual_start_dates) : (!empty($min_start_date) ? $min_start_date : date('Y-m-d', strtotime('-8 weeks')));

echo "actual_start_dates: " . json_encode($actual_start_dates) . "\n";
echo "min từ actual: " . min($actual_start_dates) . "\n";
echo "start_date final: " . $start_date . "\n";
echo "end_date: " . date('Y-m-d') . "\n";

// Test 2: Thử generate_week_dates giả lập
function test_generate_week_dates($start_date, $end_date)
{
  $weeks = [];

  // Tìm ngày Thứ Hai đầu tiên (bắt đầu tuần)
  $current = strtotime($start_date);
  $day_of_week = date('N', $current); // 1 (Thứ Hai) đến 7 (Chủ Nhật)

  echo "start_date: $start_date, day_of_week: $day_of_week\n";

  // Lùi về Thứ Hai gần nhất nếu không phải Thứ Hai
  if ($day_of_week != 1) {
    $current = strtotime('last Monday', $current);
    echo "Lùi về thứ Hai: " . date('Y-m-d', $current) . "\n";
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
      'label' => "Tuần $week_start đến $week_end"
    ];

    // Chuyển sang tuần tiếp theo (Thứ Hai)
    $current = strtotime('+7 days', $current);
  }

  return $weeks;
}

echo "\n=== TEST GENERATE_WEEK_DATES ===\n";
$weeks = test_generate_week_dates($start_date, date('Y-m-d'));
echo "Số tuần: " . count($weeks) . "\n";
foreach ($weeks as $i => $week) {
  echo "$i: {$week['label']}\n";
}

// Test 3: Thử với fallback cũ  
echo "\n=== SO SÁNH VỚI FALLBACK CŨ ===\n";
$old_start = date('Y-m-d', strtotime('-8 weeks'));
echo "Fallback cũ (-8 weeks): $old_start\n";
$old_weeks = test_generate_week_dates($old_start, date('Y-m-d'));
echo "Số tuần (cũ): " . count($old_weeks) . "\n";
foreach ($old_weeks as $i => $week) {
  echo "$i: {$week['label']}\n";
}
