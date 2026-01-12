<?php
    class Publicacion {
        // Atributos
        private ?int $PublicacionId = null;
        private int $UsuarioId;
        private string $Contenido;
        private ?string $ImagenUrl;
        private DateTime $FechaPublicacion;
        private string $Visibilidad;

        // Constructor
        public function __construct(
            int $pUsuarioId = 0,
            string $pContenido = "",
            ?string $pImagenUrl = null,
            ?DateTime $pFechaPublicacion = null,
            string $pVisibilidad = "publica"
        ) {
            $this->UsuarioId = $pUsuarioId;
            $this->Contenido = $pContenido;
            $this->ImagenUrl = $pImagenUrl;
            $this->FechaPublicacion = $pFechaPublicacion ?? new DateTime();
            $this->Visibilidad = $pVisibilidad;
            $this->PublicacionId = null;
        }

        // Getters y Setters
        public function getPublicacionId() {
            return $this->PublicacionId;
        }
        public function setPublicacionId(int $pPublicacionId) {
            $this->PublicacionId = $pPublicacionId;
        }

        public function getUsuarioId() {
            return $this->UsuarioId;
        }
        public function setUsuarioId(int $pUsuarioId) {
            $this->UsuarioId = $pUsuarioId;
        }

        public function getContenido() {
            return $this->Contenido;
        }
        public function setContenido(string $pContenido) {
            $this->Contenido = $pContenido;
        }

        public function getImagenUrl() {
            return $this->ImagenUrl;
        }
        public function setImagenUrl(?string $pImagenUrl) {
            $this->ImagenUrl = $pImagenUrl;
        }

        public function getFechaPublicacion() {
            return $this->FechaPublicacion;
        }
        public function setFechaPublicacion(DateTime $pFechaPublicacion) {
            $this->FechaPublicacion = $pFechaPublicacion;
        }

        public function getVisibilidad() {
            return $this->Visibilidad;
        }
        public function setVisibilidad(string $pVisibilidad) {
            $this->Visibilidad = $pVisibilidad;
        }
    }
