<?php

class Amigo {

    // Estados válidos (ENUM)
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_ACEPTADO  = 'aceptado';
    public const ESTADO_BLOQUEADO = 'bloqueado';

    private ?int $amigoId = null;
    private int $usuarioId;
    private int $amigoUsuarioId;
    private string $estado;
    private DateTime $fechaSolicitud;
    private ?DateTime $fechaAceptacion;

    public function __construct(
        int $usuarioId,
        int $amigoUsuarioId,
        string $estado = self::ESTADO_PENDIENTE,
        ?DateTime $fechaSolicitud = null,
        ?DateTime $fechaAceptacion = null
    ) {
        $this->usuarioId = $usuarioId;
        $this->amigoUsuarioId = $amigoUsuarioId;
        $this->setEstado($estado); // VALIDACIÓN
        $this->fechaSolicitud = $fechaSolicitud ?? new DateTime();
        $this->fechaAceptacion = $fechaAceptacion;
    }

    // Getters / Setters tipados

    public function getAmigoId(): ?int {
        return $this->amigoId;
    }

    public function setAmigoId(int $amigoId): void {
        $this->amigoId = $amigoId;
    }

    public function getUsuarioId(): int {
        return $this->usuarioId;
    }

    public function setUsuarioId(int $usuarioId): void {
        $this->usuarioId = $usuarioId;
    }

    public function getAmigoUsuarioId(): int {
        return $this->amigoUsuarioId;
    }

    public function setAmigoUsuarioId(int $amigoUsuarioId): void {
        $this->amigoUsuarioId = $amigoUsuarioId;
    }

    public function getEstado(): string {
        return $this->estado;
    }

    public function setEstado(string $estado): void {
        $estadosValidos = [
            self::ESTADO_PENDIENTE,
            self::ESTADO_ACEPTADO,
            self::ESTADO_BLOQUEADO
        ];

        if (!in_array($estado, $estadosValidos, true)) {
            throw new InvalidArgumentException("Estado no válido");
        }

        $this->estado = $estado;
    }

    public function getFechaSolicitud(): DateTime {
        return $this->fechaSolicitud;
    }

    public function setFechaSolicitud(DateTime $fechaSolicitud): void {
        $this->fechaSolicitud = $fechaSolicitud;
    }

    public function getFechaAceptacion(): ?DateTime {
        return $this->fechaAceptacion;
    }

    public function setFechaAceptacion(?DateTime $fechaAceptacion): void {
        $this->fechaAceptacion = $fechaAceptacion;
    }
}
