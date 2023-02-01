<?php

namespace App\Dto;

class CreateUserDto
{


    private string $id ;

    private string $username ;

    private string $password ;

    private  string $rolName;

    public function __construct()
    {
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
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
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




}