<?php
    class Comentario {
        // Atributos
        private ?int $ComentarioId = null;
        private int $PublicacionId;
        private int $UsuarioId;
        private string $Contenido;
        private DateTime $FechaComentario;

        // Constructor
        public function __construct(
            int $pPublicacionId = 0,
            int $pUsuarioId = 0,
            string $pContenido = "",
            ?DateTime $pFechaComentario = null
        ) {
            $this->PublicacionId = $pPublicacionId;
            $this->UsuarioId = $pUsuarioId;
            $this->Contenido = $pContenido;
            $this->FechaComentario = $pFechaComentario ?? new DateTime();
            $this->ComentarioId = null;
        }

        // Getters y Setters
        public function getComentarioId() {
            return $this->ComentarioId;
        }
        public function setComentarioId(int $pComentarioId) {
            $this->ComentarioId = $pComentarioId;
        }

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

        public function getFechaComentario() {
            return $this->FechaComentario;
        }
        public function setFechaComentario(DateTime $pFechaComentario) {
            $this->FechaComentario = $pFechaComentario;
        }
    }
