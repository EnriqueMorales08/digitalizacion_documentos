document.addEventListener('DOMContentLoaded', function() {
    const agenciaSelect = document.getElementById('agencia');
    const responsableSelect = document.getElementById('nombre_responsable');
    const centroCostoSelect = document.getElementById('centro_costo');

    // Función para limpiar y poblar un select con opciones (deduplicadas)
    function poblarSelect(select, opciones, placeholder = '-- Seleccione --') {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        // Usar Set para eliminar duplicados
        const opcionesUnicas = [...new Set(opciones)];
        opcionesUnicas.forEach(opcion => {
            if (opcion) { // Evitar opciones vacías
                const option = document.createElement('option');
                option.value = opcion;
                option.textContent = opcion;
                select.appendChild(option);
            }
        });
    }

    // 1. Cargar agencias al cargar la página (con deduplicación)
    fetch('/digitalizacion-documentos/documents/get-agencias')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(agencias => {
            poblarSelect(agenciaSelect, agencias, '-- Seleccione Agencia --');
        })
        .catch(error => {
            console.error('Error cargando agencias:', error);
            alert('No se pudieron cargar las agencias. Revisa la consola para más detalles. Por favor recargue la página.');
        });

    // 2. Al cambiar agencia, cargar responsables correspondientes (con deduplicación)
    agenciaSelect.addEventListener('change', function() {
        const agenciaSeleccionada = this.value;
        responsableSelect.disabled = true;
        centroCostoSelect.disabled = true;

        if (agenciaSeleccionada) {
            fetch(`/digitalizacion-documentos/documents/get-nombres-por-agencia?agencia=${encodeURIComponent(agenciaSeleccionada)}`)
                .then(response => response.json())
                .then(nombres => {
                    poblarSelect(responsableSelect, nombres, '-- Seleccione Responsable --');
                    responsableSelect.disabled = false;
                })
                .catch(error => console.error('Error cargando responsables:', error));
        } else {
            poblarSelect(responsableSelect, [], '-- Seleccione Responsable --');
            poblarSelect(centroCostoSelect, [], '-- Seleccione Centro --');
        }
    });

    // 3. Al cambiar responsable, cargar centros de costo correspondientes (con deduplicación)
    let centrosData = []; // Guardar datos completos de centros
    
    responsableSelect.addEventListener('change', function() {
        const agenciaSeleccionada = agenciaSelect.value;
        const responsableSeleccionado = this.value;
        centroCostoSelect.disabled = true;
        document.getElementById('email_centro_costo').value = '';

        if (responsableSeleccionado && agenciaSeleccionada) {
            fetch(`/digitalizacion-documentos/documents/get-centros-costo-por-nombre?agencia=${encodeURIComponent(agenciaSeleccionada)}&nombre=${encodeURIComponent(responsableSeleccionado)}`)
                .then(response => response.json())
                .then(centros => {
                    centrosData = centros; // Guardar datos completos
                    
                    // Limpiar y poblar el select de centros de costo
                    centroCostoSelect.innerHTML = '<option value="">-- Seleccione Centro --</option>';
                    centros.forEach(centro => {
                        const option = document.createElement('option');
                        const centroCosto = centro['CENTRO DE COSTO'] || centro['CENTRO_COSTO'];
                        option.value = centroCosto;
                        option.textContent = centroCosto + (centro['NOMBRE_CC'] ? ' - ' + centro['NOMBRE_CC'] : '');
                        option.dataset.email = centro['EMAIL'] || '';
                        centroCostoSelect.appendChild(option);
                    });
                    
                    centroCostoSelect.disabled = false;
                })
                .catch(error => console.error('Error cargando centros de costo:', error));
        } else {
            poblarSelect(centroCostoSelect, [], '-- Seleccione Centro --');
        }
    });
    
    // 4. Al cambiar centro de costo, guardar el email correspondiente
    centroCostoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.email) {
            document.getElementById('email_centro_costo').value = selectedOption.dataset.email;
            console.log('Email del centro de costo guardado:', selectedOption.dataset.email);
        } else {
            document.getElementById('email_centro_costo').value = '';
        }
    });
});
