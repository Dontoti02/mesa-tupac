/**
 * SISTEMA DE MESA DE PARTES VIRTUAL — IESP TÚPAC AMARU CUSCO
 * Lógica del Formulario Único de Trámite (FUT) Digital
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Mostrar/Ocultar Campos Académicos según condición
    const radioSi = document.getElementById('es_estudiante_si');
    const radioNo = document.getElementById('es_estudiante_no');
    const academicSection = document.getElementById('seccion_academica');

    function toggleAcademicSection() {
        if (radioSi && academicSection) {
            if (radioSi.checked) {
                academicSection.style.display = 'block';
                academicSection.querySelectorAll('input, select').forEach(function(input) {
                    if (input.dataset.requiredIfAcademic) {
                        input.setAttribute('required', 'required');
                    }
                });
            } else {
                academicSection.style.display = 'none';
                academicSection.querySelectorAll('input, select').forEach(function(input) {
                    input.removeAttribute('required');
                });
            }
        }
    }

    if (radioSi && radioNo) {
        radioSi.addEventListener('change', toggleAcademicSection);
        radioNo.addEventListener('change', toggleAcademicSection);
        toggleAcademicSection();
    }

    // 2. Información dinámica del tipo de trámite seleccionado
    const selectTramite = document.getElementById('tipo_tramite_id');
    const infoBox = document.getElementById('info_tramite_box');
    const infoRequisitos = document.getElementById('tramite_requisitos');
    const infoPlazo = document.getElementById('tramite_plazo');
    const infoCosto = document.getElementById('tramite_costo');

    if (selectTramite && infoBox) {
        selectTramite.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const requisitos = selectedOption.dataset.requisitos || 'Formulario Único de Trámite (FUT).';
                const plazo = selectedOption.dataset.plazo || '5';
                const costo = selectedOption.dataset.costo || '0.00';

                if (infoRequisitos) infoRequisitos.textContent = requisitos;
                if (infoPlazo) infoPlazo.textContent = plazo + ' días hábiles';
                if (infoCosto) infoCosto.textContent = parseFloat(costo) > 0 ? 'S/ ' + parseFloat(costo).toFixed(2) : 'Gratuito';

                infoBox.style.display = 'block';
            } else {
                infoBox.style.display = 'none';
            }
        });

        // Disparar evento si ya viene preseleccionado desde el catálogo
        if (selectTramite.value) {
            selectTramite.dispatchEvent(new Event('change'));
        }
    }

    // 3. Indicador de archivos seleccionados
    const fileInput = document.getElementById('adjuntos');
    const fileListContainer = document.getElementById('lista_adjuntos');

    if (fileInput && fileListContainer) {
        fileInput.addEventListener('change', function() {
            fileListContainer.innerHTML = '';
            if (this.files && this.files.length > 0) {
                const list = document.createElement('ul');
                list.className = 'list-group list-group-flush small mt-2';

                Array.from(this.files).forEach(function(file) {
                    const item = document.createElement('li');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center py-2 px-3 bg-light border rounded mb-1';
                    
                    const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                    item.innerHTML = `<span><i class="bi bi-paperclip me-1 text-primary"></i> ${file.name}</span> <span class="badge bg-secondary">${sizeMb} MB</span>`;
                    list.appendChild(item);
                });

                fileListContainer.appendChild(list);
            }
        });
    }
});
