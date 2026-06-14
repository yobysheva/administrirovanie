document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('togglePhotoBtn');
  const extraPhoto = document.getElementById('extraPhoto');

  if (toggleBtn && extraPhoto) {
    toggleBtn.addEventListener('click', () => {
      extraPhoto.classList.toggle('open');
    });
  }
});
