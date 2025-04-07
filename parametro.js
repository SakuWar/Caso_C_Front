// parametro.js
// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    // Obtiene el parámetro 'nombre' de la URL
    const urlParams = new URLSearchParams(window.location.search);
    let carpetaNombre = urlParams.get('nombre');
    
    // Si no existe el parámetro 'nombre', genera un nombre aleatorio y actualiza la URL
    if (!carpetaNombre) {
        carpetaNombre = generarCadenaAleatoria();
        const newUrl = window.location.href.includes('?') 
            ? `${window.location.href}&nombre=${carpetaNombre}` 
            : `${window.location.href}?nombre=${carpetaNombre}`;
        window.location.href = newUrl;
    }

    // Obtiene elementos del DOM
    const dropArea = document.getElementById('drop-area');
    const form = document.getElementById('form');
    const fileInput = document.getElementById('archivo');

    // Configura los eventos necesarios
    setupEventListeners();

    // Función para configurar los eventos
    function setupEventListeners() {
        // Eventos de arrastrar y soltar
        dropArea.addEventListener('dragover', handleDragOver);
        dropArea.addEventListener('dragleave', handleDragLeave);
        dropArea.addEventListener('drop', handleDrop);
        
        // Evento para seleccionar archivos desde el input
        fileInput.addEventListener('change', handleFileInput);
        
        // Evento para el envío del formulario
        form.addEventListener('submit', handleFormSubmit);
    }

    // Maneja el evento de arrastrar archivos sobre el área
    function handleDragOver(e) {
        e.preventDefault();
        dropArea.classList.add('drag-over');
    }

    // Maneja el evento de salir del área de arrastre
    function handleDragLeave() {
        dropArea.classList.remove('drag-over');
    }

    // Maneja el evento de soltar archivos en el área
    function handleDrop(e) {
        e.preventDefault();
        dropArea.classList.remove('drag-over');
        handleFiles(e.dataTransfer.files); // Procesa los archivos soltados
    }

    // Maneja el evento de selección de archivos desde el input
    function handleFileInput(e) {
        handleFiles(e.target.files); // Procesa los archivos seleccionados
    }

    // Maneja el evento de envío del formulario
    function handleFormSubmit(e) {
        e.preventDefault();
        if (fileInput.files.length > 0) {
            handleFiles(fileInput.files); // Procesa los archivos seleccionados
        } else {
            alert('Por favor, selecciona al menos un archivo.');
        }
    }

    // Procesa una lista de archivos
    async function handleFiles(files) {
        try {
            // Sube todos los archivos de forma concurrente
            await Promise.all([...files].map(uploadFile));
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Sube un archivo al servidor
    async function uploadFile(file) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('archivo', file); // Agrega el archivo al FormData
            formData.append('nombre', carpetaNombre); // Agrega el nombre de la carpeta al FormData

            const xhr = new XMLHttpRequest(); // Crea una nueva solicitud XMLHttpRequest
            const progressBar = createProgressBar(file.name); // Crea una barra de progreso para el archivo

            // Actualiza la barra de progreso durante la subida
            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    updateProgressBar(progressBar, percent);
                }
            };

            // Maneja la respuesta del servidor
            xhr.onload = () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText); // Convierte la respuesta a JSON
                    if (response.success) {
                        completeProgressBar(progressBar, true); // Marca la barra como completada con éxito
                        setTimeout(() => window.location.reload(), 1500); // Recarga la página después de 1.5 segundos
                        resolve();
                    } else {
                        completeProgressBar(progressBar, false); // Marca la barra como fallida
                        reject(response.message); // Rechaza la promesa con el mensaje de error
                    }
                }
            };

            // Maneja errores de conexión
            xhr.onerror = () => {
                completeProgressBar(progressBar, false); // Marca la barra como fallida
                reject(new Error('Error de conexión')); // Rechaza la promesa con un error de conexión
            };

            // Configura y envía la solicitud
            xhr.open('POST', 'subir.php', true); // Configura la solicitud para enviar datos a 'subir.php'
            xhr.send(formData); // Envía el FormData con el archivo y el nombre de la carpeta
        });
    }

    // Crea una barra de progreso para un archivo
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
        
        document.getElementById('file-list').prepend(container); // Agrega la barra al inicio de la lista de archivos
        return {
            container,
            bar: container.querySelector('.progress-bar'),
            percentage: container.querySelector('.percentage')
        };
    }

    // Actualiza el progreso de la barra
    function updateProgressBar(progressBar, percent) {
        progressBar.bar.style.width = `${percent}%`; // Ajusta el ancho de la barra
        progressBar.percentage.textContent = `${percent}%`; // Actualiza el texto del porcentaje
    }

    // Completa la barra de progreso (éxito o error)
    function completeProgressBar(progressBar, success) {
        progressBar.bar.style.width = '100%'; // Llena la barra al 100%
        progressBar.bar.style.backgroundColor = success ? '#23da95' : '#ff2b00'; // Cambia el color según el resultado
        progressBar.percentage.textContent = success ? '¡Listo!' : 'Error'; // Muestra el estado final
    }

    // Genera un nombre aleatorio para la carpeta
    function generarCadenaAleatoria() {
        const caracteres = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return Array.from({length: 3}, () => 
            caracteres.charAt(Math.floor(Math.random() * caracteres.length))
        ).join(''); // Genera una cadena de 3 caracteres aleatorios
    }
});