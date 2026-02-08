
document.getElementById("form").addEventListener("submit", function(event) {
    let valido = true;

    function contieneXSS(valor) {
        const patron = /<|>|script|onerror|onload|javascript:/i;
        return patron.test(valor);
    }

    let inputNombre = document.getElementById("nombre");
    let inputApellido = document.getElementById("apellido");
    let inputUsuario = document.getElementById("usuario");
    let inputEmail = document.getElementById("email");
    let inputContra = document.getElementById("contrasena");
    let inputFecha = document.getElementById("FechaNacimiento");

    let inputAmigo = document.getElementById("formAmigo");

    // Limpiar errores
    let errorNombre = document.getElementById("error-nombre");
    let errorApellido = document.getElementById("error-apellido");
    let errorEmail = document.getElementById("error-email");
    let errorUsu = document.getElementById("error-usu");
    let errorContra = document.getElementById("error-contra");
    let errorFecha = document.getElementById("error-fecha");
    

    errorNombre.textContent = "";
    errorApellido.textContent = "";
    errorEmail.textContent = "";
    errorUsu.textContent = "";
    errorContra.textContent = "";
    errorFecha.textContent = "";

    function limpiar(input, error) {
    input.classList.remove("is-invalid");
    error.textContent = "";
    }

    limpiar(inputNombre, errorNombre);
    limpiar(inputApellido, errorApellido);
    limpiar(inputUsuario, errorUsu);
    limpiar(inputEmail, errorEmail);
    limpiar(inputContra, errorContra);
    limpiar(inputFecha, errorFecha);

    // Validar nombre
    let nombre = document.getElementById("nombre").value.trim();
    if (nombre === "") {
        errorNombre.textContent = "El nombre es obligatorio";
        inputNombre.classList.add("is-invalid");
        valido = false;
    } else if (contieneXSS(nombre)) {
        errorNombre.textContent = "El nombre contiene caracteres no permitidos";
        inputNombre.classList.add("is-invalid");
        valido = false;
    }

    // Validar  apellido
    let apellido = document.getElementById("apellido").value.trim();
    if (apellido === "") {
        errorApellido.textContent = "El apellido es obligatorio";
        inputApellido.classList.add("is-invalid");
        valido = false;
    } else if (contieneXSS(apellido)) {
        errorApellido.textContent = "El apellido contiene caracteres no permitidos";
        inputApellido.classList.add("is-invalid");
        valido = false;
    }

    // Validar Usuario
    let usuario = document.getElementById("usuario").value.trim();
    let regexUsuario = /^[a-zA-Z0-9]{4,}$/;

    if (usuario === "") {
        errorUsu.textContent = "El usuario es obligatorio";
        inputUsuario.classList.add("is-invalid");
        valido = false;
    } 
    else if (!regexUsuario.test(usuario)) {
        errorUsu.textContent = "El usuario debe tener al menos 4 caracteres y solo puede contener letras y números";
        inputUsuario.classList.add("is-invalid");
        valido = false;
    }

    // Validar Fecha
    const fechaInput = document.getElementById("FechaNacimiento");

    if (fechaInput.value === "") {
        errorFecha.textContent = "La fecha de nacimiento es obligatoria";
        inputFecha.classList.add("is-invalid");
        valido = false;
    } else {
        const fechaNacimiento = new Date(fechaInput.value);
        const hoy = new Date();

        let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        const mes = hoy.getMonth() - fechaNacimiento.getMonth();

        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
            edad--;
        }

        if (edad < 16) {
            errorFecha.textContent = "Debes tener al menos 16 años";
            inputFecha.classList.add("is-invalid");
            valido = false;
        }
    }

   

        // Validar contraseña
    let contrasena = document.getElementById("contrasena").value.trim();

    if (contrasena === "") {
        errorContra.textContent = "La contraseña es obligatoria";
        inputContra.classList.add("is-invalid");
        valido = false;
    } 
    else if (contieneXSS(contrasena)) {
        errorContra.textContent = "La contraseña contiene caracteres no permitidos";
        inputContra.classList.add("is-invalid");
        valido = false;
    } 
    else {
        let regexContrasena = /^(?=.*[A-Z])(?=.*\d).{7,}$/;

        if (!regexContrasena.test(contrasena)) {
            errorContra.textContent =
                "La contraseña debe tener al menos 7 caracteres, una mayúscula y un número";
            inputContra.classList.add("is-invalid");
            valido = false;
        } else {
            errorContra.textContent = "";
            inputContra.classList.remove("is-invalid");
        }
    }


    // Validar email
    let email = document.getElementById("email").value.trim();
    if (email === "") {
      errorEmail.textContent = "El correo electrónico es obligatorio";
      inputEmail.classList.add("is-invalid");
      valido = false;
    } else {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        errorEmail.textContent = "La dirección de correo electrónico no tiene un formato correcto.";
        inputEmail.classList.add("is-invalid");
        valido = false;
      }
    }
    
    if (!valido) {
        event.preventDefault();
    }

    [inputNombre, inputApellido, inputUsuario, inputEmail, inputContra, inputFecha]
    .forEach(input => {
        input.addEventListener("input", () => {
            input.classList.remove("is-invalid");
        });
    });

});

