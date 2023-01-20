<?php

namespace App\Entity;

use App\Repository\RolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RolRepository::class)]
class Rol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_query'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user_query'])]
    private ?string $identidicador = null;

    #[ORM\Column(length: 150)]
    #[Groups(['user_query'])]
    private ?string $descripcion = null;

    #[ORM\OneToMany(mappedBy: 'rol', targetEntity: Usuario::class, orphanRemoval: true)]
    private Collection $usuarios;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentidicador(): ?string
    {
        return $this->identidicador;
    }

    public function setIdentidicador(string $identidicador): self
    {
        $this->identidicador = $identidicador;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection<int, Usuario>
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios->add($usuario);
            $usuario->setRol($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getRol() === $this) {
                $usuario->setRol(null);
            }
        }

        return $this;
    }
}
