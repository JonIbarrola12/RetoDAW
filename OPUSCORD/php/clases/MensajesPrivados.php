<?php

class MensajesPrivados {

    private ?int $mensajeId = null;
    private int $emisorId;
    private int $receptorId;
    private string $contenido;
    private DateTime $fechaEnvio;
    private bool $leido;

    public function __construct(
        int $emisorId,
        int $receptorId,
        string $contenido,
        ?DateTime $fechaEnvio = null,
        bool $leido = false
    ) {
        $this->emisorId = $emisorId;
        $this->receptorId = $receptorId;
        $this->setContenido($contenido);
        $this->fechaEnvio = $fechaEnvio ?? new DateTime();
        $this->leido = $leido;
    }

    public function getMensajeId(): ?int {
        return $this->mensajeId;
    }

    public function setMensajeId(int $mensajeId): void {
        $this->mensajeId = $mensajeId;
    }

    public function getEmisorId(): int {
        return $this->emisorId;
    }

    public function setEmisorId(int $emisorId): void {
        $this->emisorId = $emisorId;
    }

    public function getReceptorId(): int {
        return $this->receptorId;
    }

    public function setReceptorId(int $receptorId): void {
        $this->receptorId = $receptorId;
    }

    public function getContenido(): string {
        return $this->contenido;
    }

    public function setContenido(string $contenido): void {
        $contenido = trim($contenido);

        if ($contenido === '') {
            throw new InvalidArgumentException("El contenido del mensaje no puede estar vacío");
        }

        if (strlen($contenido) > 500) {
            throw new InvalidArgumentException("El contenido del mensaje no puede superar 500 caracteres");
        }

        $this->contenido = $contenido;
    }

    public function getFechaEnvio(): DateTime {
        return $this->fechaEnvio;
    }

    public function setFechaEnvio(DateTime $fechaEnvio): void {
        $this->fechaEnvio = $fechaEnvio;
    }

    public function getLeido(): bool {
        return $this->leido;
    }

    public function setLeido(bool $leido): void {
        $this->leido = $leido;
    }
}
