const toggle = document.querySelector(".BaGach");
const sidebar = document.querySelector(".MenuNho");

toggle.addEventListener("click", () => {
  sidebar.classList.toggle("rongra");
});

//Thông báo
function hienThiThongBao2(soLuong) {
  const chuong = document.querySelector(".menufull a i.fa-bell");
  const sotb = document.querySelector(".SoThongBao");
  if (soLuong > 0) {
    sotb.style.display = "inline-block";
    sotb.textContent = soLuong >10 ? "10+" : soLuong;
    chuong.style.marginLeft = "24px";
  } else {
    sotb.style.display = "none";
    chuong.style.marginRight = "0";
  }
}