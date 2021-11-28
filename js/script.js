document.addEventListener('DOMContentLoaded', () => {
  const clipboard = new ClipboardJS('.copy-btn');
  const myToastEl = document.querySelector('.toast');
  const toastText = myToastEl.querySelector('.toast-body');

  clipboard.on('success', event => {
    toastText.textContent = `Ссылка '${event.text}' скопирована в буфер`;

    const myToast = new bootstrap.Toast(myToastEl);
    myToast.show();

    event.clearSelection();
  });
});
