MÔ TẢ CÔNG VIỆC: BẢN CẬP NHẬT HỆ THỐNG QUẢN LÝ NHÂN SỰ
Bạn đóng vai trò là một Fullstack Developer. Hãy đọc kỹ từng yêu cầu dưới đây và thực hiện việc chỉnh sửa/thêm mới code từng bước một, đảm bảo không phá vỡ logic cũ.

PHẦN 1: SỬA LỖI (BUG FIXES) VÀ CHUẨN HÓA DỮ LIỆU
Sửa lỗi xuất Excel (Missing Library & Encoding):

Bổ sung thư viện openpyxl vào requirements/package.json (hoặc cài đặt nếu cần) để giải quyết lỗi không xuất được file Excel.

Sửa lỗi font chữ/mất dấu tiếng Việt trên trang báo cáo và trong file xuất ra. Đảm bảo sử dụng chuẩn encoding UTF-8 khi đọc/ghi file hoặc render dữ liệu.

Làm sạch dữ liệu địa điểm (Data Cleaning):

Hiện tại field địa điểm đang chứa thông tin rác. Ví dụ: "Đường D9, Phường Tây Thạnh, Thuận An | 10.812511, 106.626582 | ±94m".

Cập nhật logic hiển thị và xử lý dữ liệu: Cắt chuỗi theo ký tự | và chỉ lấy phần tử đầu tiên, đồng thời trim() khoảng trắng. Kết quả mong muốn chỉ còn: "Đường D9, Phường Tây Thạnh, Thuận An".

Cập nhật logic xuất Excel:

Kiểm tra chức năng Xuất Excel hiện tại. Nếu đang xuất toàn bộ database, hãy sửa lại thành chỉ xuất theo danh sách đã được filter (xuất chính xác những data đang được hiển thị trên giao diện UI hiện tại).

PHẦN 2: PHÁT TRIỂN TÍNH NĂNG ĐỒNG BỘ MỚI (SYNC & VERIFY)
1. Thêm Button mới trên UI:

Tại trang /dong-bo-nhan-vien-online, ngay cạnh button "Đồng bộ data từ ERP", tạo thêm một button mới tên là: "Đồng bộ và kiểm tra với hệ thống".

2. Tạo Module/Sidebar xử lý đồng bộ mới:

Khi click vào button mới tạo ở trên, chuyển hướng (hoặc mở sidebar module) sang route /xu-ly-dong-bo.

UI Design cho /xu-ly-dong-bo: - Sử dụng giao diện Grid list data (Bảng dữ liệu).

Chia thành các Tabs Status để phân loại trạng thái dữ liệu.

TUYỆT ĐỐI KHÔNG hiển thị sẵn Employee Info Card trên giao diện này như trang cũ. Thay vào đó, mỗi dòng (row) sẽ có một nút "Xem".

Khi click "Xem", sẽ mở một Popup Modal hiển thị chi tiết thông tin nhân viên.

3. Logic xử lý của nút "Đồng bộ và kiểm tra với hệ thống":

Khi kích hoạt, hệ thống sẽ gọi API fetch data từ ERP (tương tự nút cũ), nhưng KHÔNG ghi ngay vào database hệ thống.

Thay vào đó, thực hiện bước Mapping & So sánh (Compare) giữa data lấy về từ ERP và data hiện có trong hệ thống để tìm ra các điểm khác biệt. Trình bày kết quả lên các Tabs Status tương ứng:

Tab 1: Nhân viên mới: Tìm ra n nhân viên có ở ERP nhưng chưa có trên hệ thống. (Cho phép xuất danh sách này ra file).

Tab 2: Sai lệch thông tin: Tìm ra các nhân viên (cùng mã/ID) nhưng khác nhau về thông tin (tên, phòng ban,...). Hiển thị chi tiết data nào bị lệch. (Cho phép xuất danh sách này ra file).

Tab 3: Sai lệch khuôn mặt (Face Image): So sánh lv007 token để phát hiện khác biệt. Tại đây, cung cấp 2 action cho người dùng chọn:

Đẩy ảnh mới từ hệ thống hiện tại lên ERP.

Hoặc cập nhật ảnh của hệ thống theo ảnh mới của ERP.

4. Ràng buộc an toàn (Safety Constraint - BẮT BUỘC):

Trước khi thực thi BẤT KỲ hành động ghi/xóa/cập nhật nào (nhấn lưu đồng bộ, thay đổi thông tin, đẩy ảnh/cập nhật ảnh), hệ thống phải hiển thị một Confirmation Modal (Hộp thoại xác nhận).

Modal này phải liệt kê/mô tả chi tiết những gì sắp bị thay đổi giữa ERP và Hệ thống để cảnh báo người dùng xác nhận trước khi gọi API thực thi.