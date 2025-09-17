document.querySelectorAll('.thumbnail').forEach(img => {
    img.addEventListener('click', () => {
        document.getElementById('mainProductImage').src = img.src;
    });
});