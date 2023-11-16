<?php

declare(strict_types=1);
namespace App\Model\Template;

use App\DAO\UsersDB;
use App\Model\Enumerate\AccountType;
use App\Model\Tool\Location;
use App\Util\Exception\InvalidAttributeLengthException;
use App\Util\Exception\InvalidAttributeRegexException;
use Exception;

/**
 * Classe modelo de usuário
 * @package Model
 * @author Ariel Santos (MrXacx)
 */
class User extends Entity
{

    use Location;

    /**
     * Nome completo do usuário
     * @var string
     */
    protected string $name;

    /**
     * Email do usuário
     * @var string
     */
    protected string $email;

    /**
     * Número de telefone do usuário
     * @var string
     */
    protected string $phone;

    /**
     * Senha do usuário
     * @var string
     */
    protected string $password;

    /**
     * site do usuário
     * @var string
     */
    protected string|null $website = null;

    /**
     * Imagem de perfil do usuário
     * @var string
     */
    protected string|null $image = null;

    /**
     * Nota média do usuário
     * @var float
     */
    protected float|string $rate;

    protected int|string $index;
    protected string $description;
    protected AccountType $type;
    protected bool|int|string $verified; // Variável para controlar se o usuário já foi verificado na aplicação servida
    
    public static function getInstanceOf(array $attr): self
    {
        $entity = new User;

        foreach ($attr as $key => $value) {

            $atributeName = match ($key) {
                'id' => 'id',
                'index', 'placing' => 'index',
                UsersDB::EMAIL => 'email',
                UsersDB::IMAGE => 'image',
                UsersDB::PASSWORD => 'password',
                UsersDB::NAME => 'name',
                UsersDB::PHONE => 'phone',
                UsersDB::CEP => 'CEP',
                UsersDB::STATE => 'state',
                UsersDB::CITY => 'city',
                UsersDB::SITE => 'website',
                UsersDB::RATE => 'rate',
                UsersDB::DESCRIPTION => 'description',
                UsersDB::VERIFIED => 'verified',

                default => null
            };

            if (isset($atributeName)) {
                $entity->$atributeName = $value;
            }

        }
        
        $entity->verified = boolval($attr[UsersDB::VERIFIED]);
        $entity->type = AccountType::tryFrom($attr[UsersDB::TYPE]);
        return $entity;
    }
    

    public function setIndex(int|string $index): void
    {
        $this->index = is_int($index) && $index >= 0 ? $index : InvalidAttributeRegexException::throw('index', __FILE__);
    }


    public function getIndex(): string|int
    {
        return $this->index;
    }

    /**
     * @param string $email Email do usuário
     */
    public function setEmail(string $email): void
    {
        $this->email = $this->validator->isEmail($email) ? $email : InvalidAttributeRegexException::throw('email', __FILE__);
    }

    /**
     * Obtém Email do usuário
     * @return string Email 
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $password Senha do usuário
     */
    public function setPassword(string $password): void
    {
        $this->password = $this->validator->isFit($password) ? $password : InvalidAttributeLengthException::throw('password', __FILE__);
    }

    /**
     * Obtém senha do usuário
     * @return string senha 
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $name Nome do usuário
     */
    public function setName(string $name): void
    {
        $this->name = $this->validator->isFit($name) ? $name : InvalidAttributeLengthException::throw('name', __FILE__);
    }

    /**
     * Obtém Nome do usuário
     * @return string nome
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @param string $phone Telefone do usuário
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $this->validator->isPhone($phone) ? $phone : InvalidAttributeRegexException::throw('phone', __FILE__);
    }

    /**
     * Obtém Número de telefone do usuário
     * @return string Número de telefone 
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $website URL do website do usuário
     */
    public function setWebsite(string $website): void
    {
        $this->website = $this->validator->isURL($website) ? $website : InvalidAttributeRegexException::throw('website', __FILE__);
    }

    /**
     * Obtém ID do usuário
     * @return string ID 
     */
    public function getWebsite(): string
    {
        return $this->website;
    }

    /**
     * @param string $image URL do imagem de perfil do usuário
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * Obtém ID do usuário
     * @return string ID 
     */
    public function getImage(): string
    {
        return $this->image;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    public function getRate(): float
    {
        return floatval($this->rate);
    }
    public function setDescription(string $description): void
    {
        $this->description = $this->validator->isFit($description, UsersDB::DESCRIPTION) ? $description : InvalidAttributeLengthException::throw('description', __FILE__);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setType(AccountType $type): void
    {
        $this->type = $type;
    }

    public function getType(): AccountType
    {
        return $this->type;
    }

    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }
    public function isVerified(): bool
    {
        return boolval($this->verified);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'index' => $this->index,
            'name' => $this->name,
            'image' => $this->image,
            'email' => $this->email ?? null,
            'password' => $this->password ?? null,
            'phone' => $this->phone ?? null,
            'location' => $this->toLocationArray(),
            'rate' => $this->rate,
            'website' => $this->website,
            'description' => $this->description,
            'type' => $this->type->value,
            'verified' => $this->isVerified(),
        ]);
    }
}
