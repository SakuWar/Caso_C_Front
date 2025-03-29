// parametro.js
document.addEventListener('DOMContentLoaded', () => {
    // Obtener parámetro 'nombre' de la URL
    const urlParams = new URLSearchParams(window.location.search);
    let carpetaNombre = urlParams.get('nombre');
    
    // Generar nombre aleatorio si no existe
    if (!carpetaNombre) {
        carpetaNombre = generarCadenaAleatoria();
        const newUrl = window.location.href.includes('?') 
            ? `${window.location.href}&nombre=${carpetaNombre}` 
            : `${window.location.href}?nombre=${carpetaNombre}`;
        window.location.href = newUrl;
    }

    // Elementos del DOM
    const dropArea = document.getElementById('drop-area');
    const form = document.getElementById('form');
    const fileInput = document.getElementById('archivo');

    // Configurar eventos
    setupEventListeners();

    function setupEventListeners() {
        // Drag and Drop
        dropArea.addEventListener('dragover', handleDragOver);
        dropArea.addEventListener('dragleave', handleDragLeave);
        dropArea.addEventListener('drop', handleDrop);
        
        // Input de archivo
        fileInput.addEventListener('change', handleFileInput);
        
        // Formulario
        form.addEventListener('submit', handleFormSubmit);
    }

    function handleDragOver(e) {
        e.preventDefault();
        dropArea.classList.add('drag-over');
    }

    function handleDragLeave() {
        dropArea.classList.remove('drag-over');
    }

    function handleDrop(e) {
        e.preventDefault();
        dropArea.classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    }

    function handleFileInput(e) {
        handleFiles(e.target.files);
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        if (fileInput.files.length > 0) {
            handleFiles(fileInput.files);
        } else {
            alert('Por favor, selecciona al menos un archivo.');
        }
    }

    async function handleFiles(files) {
        try {
            await Promise.all([...files].map(uploadFile));
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function uploadFile(file) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('archivo', file);
            formData.append('nombre', carpetaNombre);

            const xhr = new XMLHttpRequest();
            const progressBar = createProgressBar(file.name);

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    updateProgressBar(progressBar, percent);
                }
            };

            xhr.onload = () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        completeProgressBar(progressBar, true);
                        setTimeout(() => window.location.reload(), 1500);
                        resolve();
                    } else {
                        completeProgressBar(progressBar, false);
                        reject(response.message);
                    }
                }
            };

            xhr.onerror = () => {
                completeProgressBar(progressBar, false);
                reject(new Error('Error de conexión'));
            };

            xhr.open('POST', 'subir.php', true);
            xhr.send(formData);
        });
    }

    function createProgressBar(fileName) {
        const container = document.createElement('div');
        container.className = 'progress-container';
        
        container.innerHTML = `
            <div class="progress-info">
                <span class="file-name">${fileName}</span>
                <span class="percentage">0%</span>
            </div>
            <div class="progress-bar"></div>
        `;
        
        document.getElementById('file-list').prepend(container);
        return {
            container,
            bar: container.querySelector('.progress-bar'),
            percentage: container.querySelector('.percentage')
        };
    }

    function updateProgressBar(progressBar, percent) {
        progressBar.bar.style.width = `${percent}%`;
        progressBar.percentage.textContent = `${percent}%`;
    }

    function completeProgressBar(progressBar, success) {
        progressBar.bar.style.width = '100%';
        progressBar.bar.style.backgroundColor = success ? '#23da95' : '#ff2b00';
        progressBar.percentage.textContent = success ? '¡Listo!' : 'Error';
    }

    function generarCadenaAleatoria() {
        const caracteres = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return Array.from({length: 3}, () => 
            caracteres.charAt(Math.floor(Math.random() * caracteres.length))
        ).join('');
    }
});