<?php
    class Miembro {
        // Atributos
        private ?int $MiembroId = null;
        private int $UsuarioId;
        private int $GrupoId;
        private string $Rol;
        private DateTime $FechaIngreso;

        // Constructor
        public function __construct(
            int $pUsuarioId = 0,
            int $pGrupoId = 0,
            string $pRol = "miembro",
            ?DateTime $pFechaIngreso = null
        ) {
            $this->UsuarioId = $pUsuarioId;
            $this->GrupoId = $pGrupoId;
            $this->Rol = $pRol;
            $this->FechaIngreso = $pFechaIngreso ?? new DateTime();
            $this->MiembroId = null;
        }

        // Getters y Setters
        public function getMiembroId() {
            return $this->MiembroId;
        }
        public function setMiembroId(int $pMiembroId) {
            $this->MiembroId = $pMiembroId;
        }

        public function getUsuarioId() {
            return $this->UsuarioId;
        }
        public function setUsuarioId(int $pUsuarioId) {
            $this->UsuarioId = $pUsuarioId;
        }

        public function getGrupoId() {
            return $this->GrupoId;
        }
        public function setGrupoId(int $pGrupoId) {
            $this->GrupoId = $pGrupoId;
        }

        public function getRol() {
            return $this->Rol;
        }
        public function setRol(string $pRol) {
            $this->Rol = $pRol;
        }

        public function getFechaIngreso() {
            return $this->FechaIngreso;
        }
        public function setFechaIngreso(DateTime $pFechaIngreso) {
            $this->FechaIngreso = $pFechaIngreso;
        }
    }
