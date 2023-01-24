<?php

namespace App\Dto;

class UserDto
{
    private int $id ;
    private string $username ;
    private  string $rolName;
    private  PerfilDto $perfilDto;

    public function __construct()
    {
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getRolName(): string
    {
        return $this->rolName;
    }

    /**
     * @param string $rolName
     */
    public function setRolName(string $rolName): void
    {
        $this->rolName = $rolName;
    }

    /**
     * @return PerfilDto
     */
    public function getPerfilDto(): PerfilDto
    {
        return $this->perfilDto;
    }

    /**
     * @param PerfilDto $perfilDto
     */
    public function setPerfilDto(PerfilDto $perfilDto): void
    {
        $this->perfilDto = $perfilDto;
    }







}