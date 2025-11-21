window.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  const error = params.get("error");
  const errorBox = document.getElementById("error-message");

  if (error === "login") {
    errorBox.textContent = "Tên đăng nhập hoặc mật khẩu không đúng, vui lòng thử lại!";
    errorBox.style.display = "block";
  } else if (error === "role") {
    errorBox.textContent = "Không xác định quyền đăng nhập!";
    errorBox.style.display = "block";
  }
});
