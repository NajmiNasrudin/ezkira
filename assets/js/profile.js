/**
 * SME Finance Monitor — Profile Page JS
 */

document.addEventListener('DOMContentLoaded', function () {
    const fileInput    = document.getElementById('avatar-input');
    const preview      = document.getElementById('avatar-preview');
    const placeholder  = document.getElementById('avatar-preview-placeholder');
    const fileChosen   = document.getElementById('file-chosen');

    if (!fileInput) return;

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Show file name
        if (fileChosen) {
            fileChosen.textContent = 'Selected: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            fileChosen.classList.remove('hidden');
        }

        // Client-side MIME check
        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) {
            if (fileChosen) {
                fileChosen.textContent = 'Error: Only JPEG, PNG, or WebP files are allowed.';
                fileChosen.classList.add('text-red-500');
            }
            this.value = '';
            return;
        }

        // Size check (2MB)
        if (file.size > 2 * 1024 * 1024) {
            if (fileChosen) {
                fileChosen.textContent = 'Error: File is too large. Max 2MB.';
                fileChosen.classList.add('text-red-500');
            }
            this.value = '';
            return;
        }

        // Preview
        const reader = new FileReader();
        reader.onload = function (e) {
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    });
});
