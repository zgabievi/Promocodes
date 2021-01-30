<?php

namespace Zorb\Promocodes;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Zorb\Promocodes\Models\Promocode;
use Symfony\Component\HttpFoundation\Response;

class Promocodes
{
    /**
     * Prefix for code generation
     *
     * @var string
     */
    protected $prefix;

    /**
     * Suffix for code generation
     *
     * @var string
     */
    protected $suffix;

    /**
     * Number of codes to be generated
     *
     * @var int
     */
    protected $amount = 1;

    /**
     * Additional data to be returned with code
     *
     * @var array
     */
    protected $data = [];

    /**
     * Number of days of code expiration
     *
     * @var null|int
     */
    protected $expires_in = null;

    /**
     * Maximum number of available usage of code
     *
     * @var null|int
     */
    protected $quantity = null;

    /**
     * Indicator that defines if auth is required
     *
     * @var bool
     */
    protected $auth_required = false;

    /**
     * If code should automatically invalidate after first use
     *
     * @var bool
     */
    protected $disposable = false;

    /**
     * Characters that should be used for code generation
     *
     * @var null|string
     */
    protected $characters = null;

    /**
     * Mask to be used to generate code pattern
     *
     * @var null|string
     */
    protected $mask = null;

    /**
     * Delimiter to be used for prefix/suffix separation
     *
     * @var null|string
     */
    protected $delimiter = null;

    /**
     * Generated codes will be saved here to be validated later
     *
     * @var array
     */
    protected $available_codes = [];

    /**
     * Length of code will be calculated from asterisks you have
     * set as mask in your config file.
     *
     * @var int
     */
    protected $length;

    /**
     * Promocodes constructor.
     */
    public function __construct()
    {
        $this->available_codes = Promocode::pluck('code')->toArray();
        $this->length = substr_count($this->getMask(), '*');
    }

    /**
     * Get mask that has been custom set
     *
     * @return string|null
     */
    public function getMask(): ?string
    {
        return $this->mask ?? config('promocodes.mask');
    }

    /**
     * Set custom mask for next code generation
     *
     * @param string $mask
     * @return self
     */
    public function setMask(string $mask): self
    {
        $this->mask = $mask;
        $this->length = substr_count($mask, '*');

        return $this;
    }

    /**
     * Set custom expiry for next code generation
     *
     * @param int $expires_in
     * @return self
     */
    public function setExpiry(int $expires_in): self
    {
        $this->expires_in = $expires_in;

        return $this;
    }

    /**
     * Create promotional codes and save them to database.
     *
     * @param array $data
     * @return \Illuminate\Support\Collection
     */
    public function create(array $data = null): Collection
    {
        return $this->output()->map(function (string $code) use ($data) {
            return Promocode::create([
                'code' => $code,
                'data' => $this->getData($data),
                'expires_at' => $this->getExpiry() ? Carbon::now()->addDays($this->getExpiry()) : null,
                'is_disposable' => $this->getDisposable(),
                'auth_required' => $this->getAuthRequired(),
                'quantity' => $this->getQuantity(),
            ]);
        });
    }

    /**
     * Outputs generated promotional codes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function output(): Collection
    {
        $collection = collect([]);

        for ($i = 1; $i <= $this->getAmount(); $i++) {
            $random = $this->generate();

            while (!$this->validate($collection, $random)) {
                $random = $this->generate();
            }

            $collection->push($random);
        }

        return $collection;
    }

    /**
     * Get amount that has been custom set
     *
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * Set custom amount for next code generation
     *
     * @param int $amount
     * @return self
     */
    public function setAmount(int $amount = 1): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Here will be generated single code using your parameters from config.
     *
     * @return string
     */
    public function generate(): string
    {
        $characters = $this->getCharacters();
        $mask = $this->getMask();
        $random = [];
        $code = '';

        for ($i = 1; $i <= $this->length; $i++) {
            $character = $characters[rand(0, strlen($characters) - 1)];
            $random[] = $character;
        }

        shuffle($random);
        $length = count($random);


        if ($prefix = $this->getPrefix()) {
            $code .= $prefix;
            $code .= $this->getDelimiter();
        }

        for ($i = 0; $i < $length; $i++) {
            $mask = preg_replace('/\*/', $random[$i], $mask, 1);
        }

        $code .= $mask;

        if ($suffix = $this->getSuffix()) {
            $code .= $this->getDelimiter();
            $code .= $suffix;
        }

        return $code;
    }

    /**
     * Get characters that has been custom set
     *
     * @return string|null
     */
    public function getCharacters(): ?string
    {
        return $this->characters ?? config('promocodes.characters');
    }

    /**
     * Set custom characters for next code generation
     *
     * @param string $characters
     * @return self
     */
    public function setCharacters(string $characters): self
    {
        $this->characters = $characters;

        return $this;
    }

    /**
     * Get prefix that has been custom set
     *
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix ?? config('promocodes.prefix');
    }

    /**
     * Set custom prefix for next code generation
     *
     * @param string $prefix
     * @return self
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get delimiter that has been custom set
     *
     * @return string|null
     */
    public function getDelimiter(): ?string
    {
        return $this->delimiter ?? config('promocodes.delimiter');
    }

    /**
     * Set custom delimiter for next code generation
     *
     * @param string $delimiter
     * @return self
     */
    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Get suffix that has been custom set
     *
     * @return string|null
     */
    public function getSuffix(): ?string
    {
        return $this->suffix ?? config('promocodes.suffix');
    }

    /**
     * Set custom suffix for next code generation
     *
     * @param string $suffix
     * @return self
     */
    public function setSuffix(string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Generated code will be validated to be unique for one request
     *
     * @param \Illuminate\Support\Collection $collection
     * @param string $code
     *
     * @return bool
     */
    public function validate(Collection $collection, string $code): bool
    {
        return !$collection->merge($this->available_codes)->contains($code);
    }

    /**
     * Get data that has been custom set
     *
     * @param array|null $initial
     * @return array|null
     */
    public function getData(?array $initial = null): ?array
    {
        return $initial ?? $this->data;
    }

    /**
     * Set custom data for next code generation
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data = []): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get expiry that has been custom set
     *
     * @return int|null
     */
    public function getExpiry(): ?int
    {
        return $this->expires_in;
    }

    /**
     * Get disposable that has been custom set
     *
     * @return bool|null
     */
    public function getDisposable(): ?bool
    {
        return $this->disposable;
    }

    /**
     * Set custom disposable for next code generation
     *
     * @param bool $disposable
     * @return self
     */
    public function setDisposable(bool $disposable = true): self
    {
        $this->disposable = $disposable;

        return $this;
    }

    /**
     * Get auth required that has been custom set
     *
     * @return bool
     */
    public function getAuthRequired(): bool
    {
        return $this->auth_required;
    }

    /**
     * Set auth required for next code generation
     *
     * @param bool $auth_required
     * @return self
     */
    public function setAuthRequired(bool $auth_required = true): self
    {
        $this->auth_required = $auth_required;

        return $this;
    }

    /**
     * Get quantity that has been custom set
     *
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Set custom quantity for next code generation
     *
     * @param int $quantity
     * @return self
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Alias for redeem method.
     *
     * @param string $code
     * @return \Zorb\Promocodes\Models\Promocode|null
     */
    public function use(string $code): ?Promocode
    {
        return $this->redeem($code);
    }

    /**
     * Use code and output data.
     *
     * @param string $code
     * @return \Zorb\Promocodes\Models\Promocode|null
     */
    public function redeem(string $code): ?Promocode
    {
        if (!auth()->check()) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if ($record = $this->available($code)) {
            if ($this->secondAttempt($record)) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $record->users()->attach(auth()->id(), [
                'used_at' => Carbon::now(),
            ]);

            if (!is_null($record->quantity)) {
                $record->decrement('quantity');
            }

            return $record->load('users');
        }

        return null;
    }

    /**
     * Returns promotional code if available to use.
     *
     * @param string $code
     * @return \Zorb\Promocodes\Models\Promocode|null
     */
    public function available(string $code): ?Promocode
    {
        $promocode = Promocode::byCode($code)->first();

        if ($promocode === null || !$promocode->isAvailable() || ($promocode->isDisposable() && $promocode->users()->exists())) {
            return null;
        }

        return $promocode;
    }

    /**
     * Alias for redeem method.
     *
     * @param string $code
     * @return \Zorb\Promocodes\Models\Promocode|null
     */
    public function apply(string $code): ?Promocode
    {
        return $this->redeem($code);
    }

    /**
     * Alias for disable.
     *
     * @param string $code
     * @return bool|null
     */
    public function dispose(string $code): ?bool
    {
        return $this->disable($code);
    }

    /**
     * Expire code as it won't be usable anymore.
     *
     * @param string $code
     * @return bool|null
     */
    public function disable(string $code): ?bool
    {
        if ($promocode = Promocode::byCode($code)->first()) {
            return $promocode->update([
                'expires_at' => Carbon::now(),
                'quantity' => 0,
            ]);
        }

        return null;
    }

    /**
     * Alias for disable.
     *
     * @param string $code
     * @return bool|null
     */
    public function expire(string $code): ?bool
    {
        return $this->disable($code);
    }

    /**
     * Clear all expired and used codes that can not be used anymore.
     *
     * @return void
     */
    public function clear(): void
    {
        Promocode::all()->each(function (Promocode $promocode) {
            if ($promocode->isExpired() || ($promocode->disposable && $promocode->users()->exists()) || !$promocode->hasQuantity()) {
                $promocode->users()->detach();
                $promocode->delete();
            }
        });
    }

    /**
     * Get the list of valid codes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allAvailable(): Collection
    {
        return Promocode::all()->filter(function (Promocode $promocode) {
            return !$promocode->isExpired() && !($promocode->disposable && $promocode->users()->exists()) && $promocode->hasQuantity();
        });
    }
}
