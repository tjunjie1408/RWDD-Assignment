    // ------------------------------
    // 切换到编辑模式
    // ------------------------------
function switchToEdit() {
    // 隐藏查看模式
    document.getElementById("viewMode").classList.add("hidden");
    // 显示编辑模式
    document.getElementById("editMode").classList.remove("hidden");
    }

    // ------------------------------
    // 取消编辑，回到查看模式
    // ------------------------------
function cancelEdit() {
    document.getElementById("editMode").classList.add("hidden");
    document.getElementById("viewMode").classList.remove("hidden");
    }

    // ------------------------------
    // 保存修改后的信息
    // ------------------------------
function saveChanges() {
    // 这里只是UI切换，数据保存逻辑以后由PHP + MySQL处理
    document.getElementById("v-first").textContent = document.getElementById("e-first").value;
    document.getElementById("v-last").textContent = document.getElementById("e-last").value;
    document.getElementById("v-email").textContent = document.getElementById("e-email").value;
    document.getElementById("v-password").textContent = "******"; // 出于安全考虑，不显示真实密码
    document.getElementById("v-company").textContent = document.getElementById("e-company").value;
    document.getElementById("v-position").textContent = document.getElementById("e-position").value;

    // 更新头像（从 Edit Mode 的预览图获取）
const newAvatar = document.getElementById("editAvatarPreview").src;
    document.getElementById("userAvatar").src = newAvatar; // header 头像
    document.getElementById("viewAvatar").src = newAvatar; // profile 页面查看模式头像

  // 切回查看模式
    cancelEdit();
}

// ------------------------------
// 头像上传 & 预览功能
// ------------------------------
// 监听 input type="file" 的变化
document.getElementById("avatarInput").addEventListener("change", function(event) {
  const file = event.target.files[0]; // 获取上传的文件
  if (file) {
    const reader = new FileReader(); // FileReader 可以把文件读取为 base64 格式
    reader.onload = function(e) {
      // 把读取到的图片数据放到预览 img 上
      document.getElementById("editAvatarPreview").src = e.target.result;
    };
    reader.readAsDataURL(file); // 把文件读取成 Data URL（base64）
  }
});
