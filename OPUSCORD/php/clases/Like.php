<?php
    class Like {
        // Atributos
        private ?int $LikeId = null;
        private int $UsuarioId;
        private int $PublicacionId;

        // Constructor
        public function __construct(
            int $pUsuarioId = 0,
            int $pPublicacionId = 0
        ) {
            $this->UsuarioId = $pUsuarioId;
            $this->PublicacionId = $pPublicacionId;
            $this->LikeId = null;
        }

        // Getters y Setters
        public function getLikeId() {
            return $this->LikeId;
        }
        public function setLikeId(int $pLikeId) {
            $this->LikeId = $pLikeId;
        }

        public function getUsuarioId() {
            return $this->UsuarioId;
        }
        public function setUsuarioId(int $pUsuarioId) {
            $this->UsuarioId = $pUsuarioId;
        }

        public function getPublicacionId() {
            return $this->PublicacionId;
        }
        public function setPublicacionId(int $pPublicacionId) {
            $this->PublicacionId = $pPublicacionId;
        }
    }
