Thieu thu vien openpyxl de xuat Excel
lỗi không xuất được excel
trong trang báo cáo còn nhiều lỗi mất dấu tiếng Việt
địa điểm còn bị nhiều thông tin rác, ví dụ như "Đường D9, Phường Tây Thạnh, Thuận An | 10.812511, 106.626582 | ±94m" thì chỉ cần lấy "Đường D9, Phường Tây Thạnh, Thuận An"

xuất excel hiện tại đang xuất theo bộ lọc hay xuất tất cả? nếu xuất tất cả hãy sửa xuất theo bộ lọc đang được chạy "data đang được hiển thị"

ở trang /dong-bo-nhan-vien-online, kế bên button Đồng bộ data từ ERP, tạo thêm một button "Đồng bộ và kiểm tra với hệ thống"
- khi dùng button này, điều đầu tiên nó làm cũng giống Đồng bộ data từ ERP, nhưng nó sẽ tiến hành bước fetch mapping với data hệ thống (không ghi vào hệ thống) và sẽ hiển thị thông báo cho người dùng biết data hệ thống và ERP có những khác nhau gì "Có n Nhân viên chưa có trong hệ thống, xuất ra danh sách Nhân viên X chưa có trong hệ thống chấm công, nhân viên có mã XXX không trùng thông tin ABC(tên, phòng ban,...) với hệ thống, xuất danh sách khách biệt, Nhân viên có khác biệt về ảnh khuôn mặt (compare lv007 token), xuất danh sách và cho phép đẩy từ hệ thống lên ảnh mới hoặc thay đổi ảnh hệ thống theo ERP" lưu ý tất cả các hành đều sẽ có môt model mô tả hành động sắp tới để cảnh báo người dùng nếu có thay đổi về hệ thống hoặc ERP
- button trên sẽ có một module sidebar riêng /xu-ly-dong-bo, giao diện grid list data kèm các tab status riêng như trên, nó sẽ không có employ info card sẵn trên UI như /dong-bo-nhan-vien-online, mà khi click "Xem" sẽ popup model hiển thị lên