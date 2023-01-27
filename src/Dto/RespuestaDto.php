<?php

namespace App\Dto;

class RespuestaDto
{
    private int $codigo;
    private string $mensaje;

    public function __construct()
    {
    }


    /**
     * @return int
     */
    public function getCodigo(): int
    {
        return $this->codigo;
    }

    /**
     * @param int $codigo
     */
    public function setCodigo(int $codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * @return string
     */
    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    /**
     * @param string $mensaje
     */
    public function setMensaje(string $mensaje): void
    {
        $this->mensaje = $mensaje;
    }



}