<?php
    class Grupo {
        // Atributos
        private ?int $GrupoId = null;
        private string $Nombre;
        private ?string $Descripcion;
        private int $CreadorId;
        private ?string $Pfp;
        private DateTime $FechaCreacion;

        // Constructor
        public function __construct(
            string $pNombre = "",
            ?string $pDescripcion = null,
            int $pCreadorId = 0,
            ?string $pPfp = null,
            ?DateTime $pFechaCreacion = null
        ) {
            $this->Nombre = $pNombre;
            $this->Descripcion = $pDescripcion;
            $this->CreadorId = $pCreadorId;
            $this->Pfp = $pPfp;
            $this->FechaCreacion = $pFechaCreacion ?? new DateTime();
            $this->GrupoId = null;
        }

        // Getters y Setters
        public function getGrupoId() {
            return $this->GrupoId;
        }
        public function setGrupoId(int $pGrupoId) {
            $this->GrupoId = $pGrupoId;
        }

        public function getNombre() {
            return $this->Nombre;
        }
        public function setNombre(string $pNombre) {
            $this->Nombre = $pNombre;
        }

        public function getDescripcion() {
            return $this->Descripcion;
        }
        public function setDescripcion(?string $pDescripcion) {
            $this->Descripcion = $pDescripcion;
        }

        public function getCreadorId() {
            return $this->CreadorId;
        }
        public function setCreadorId(int $pCreadorId) {
            $this->CreadorId = $pCreadorId;
        }

        public function getPfp() {
            return $this->Pfp;
        }
        public function setPfp(?string $pPfp) {
            $this->Pfp = $pPfp;
        }

        public function getFechaCreacion() {
            return $this->FechaCreacion;
        }
        public function setFechaCreacion(DateTime $pFechaCreacion) {
            $this->FechaCreacion = $pFechaCreacion;
        }
    }
