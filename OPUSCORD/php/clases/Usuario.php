<?php
    class Usuario {
        // Atributos
        private ?int $UsuarioId = null;
        private ?string $Nombre;
        private ?string $Apellido;
        private string $Username;
        private string $Email;
        private string $Password;
        private ?string $Pfp;
        private string $Bio;
        private DateTime $FechaRegistro;

        // Constructor
        public function __construct(
            ?string $pNombre = null,
            ?string $pApellido = null,
            string $pUsername = "",
            string $pEmail = "",
            string $pPassword = "",
            ?string $pPfp = null,
            string $pBio = "Estoy usando OPUSCORD!",
            ?DateTime $pFechaRegistro = null
        ) {
            $this->Nombre = $pNombre;
            $this->Apellido = $pApellido;
            $this->Username = $pUsername;
            $this->Email = $pEmail;
            $this->Password = $pPassword;
            $this->Pfp = $pPfp;
            $this->Bio = $pBio;
            $this->FechaRegistro = $pFechaRegistro ?? new DateTime();
            $this->UsuarioId = null;
        }

        // Getters y Setters
        public function getUsuarioId() {
            return $this->UsuarioId;
        }
        public function setUsuarioId(int $pUsuarioId) {
            $this->UsuarioId = $pUsuarioId;
        }

        public function getNombre() {
            return $this->Nombre;
        }
        public function setNombre(?string $pNombre) {
            $this->Nombre = $pNombre;
        }

        public function getApellido() {
            return $this->Apellido;
        }
        public function setApellido(?string $pApellido) {
            $this->Apellido = $pApellido;
        }

        public function getUsername() {
            return $this->Username;
        }
        public function setUsername(string $pUsername) {
            $this->Username = $pUsername;
        }

        public function getEmail() {
            return $this->Email;
        }
        public function setEmail(string $pEmail) {
            $this->Email = $pEmail;
        }

        public function getPassword() {
            return $this->Password;
        }
        public function setPassword(string $pPassword) {
            $this->Password = $pPassword;
        }

        public function getPfp() {
            return $this->Pfp;
        }
        public function setPfp(?string $pPfp) {
            $this->Pfp = $pPfp;
        }

        public function getBio() {
            return $this->Bio;
        }
        public function setBio(string $pBio) {
            $this->Bio = $pBio;
        }

        public function getFechaRegistro() {
            return $this->FechaRegistro;
        }
        public function setFechaRegistro(DateTime $pFechaRegistro) {
            $this->FechaRegistro = $pFechaRegistro;
        }
    }
