function togglePassword(fieldId) {
    // Lấy phần tử input và biểu tượng mắt theo ID truyền vào
    const passwordInput = document.getElementById(fieldId);
    const eyeIcon = document.getElementById('eye-icon-' + fieldId);

    // Kiểm tra loại của input, nếu là "password", chuyển thành "text", ngược lại là "password"
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';  // Hiển thị mật khẩu
        eyeIcon.classList.remove('fa-eye');  // Xóa biểu tượng mắt
        eyeIcon.classList.add('fa-eye-slash');  // Thêm biểu tượng mắt bị chéo
    } else {
        passwordInput.type = 'password';  // Ẩn mật khẩu
        eyeIcon.classList.remove('fa-eye-slash');  // Xóa biểu tượng mắt bị chéo
        eyeIcon.classList.add('fa-eye');  // Thêm biểu tượng mắt
    }
}
