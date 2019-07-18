<?php
/**
 * clp-php-sdk
 * TimeSchedule.phpCopyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-31, 11:28
 */

namespace Clay\CLP\Structs;


use Illuminate\Contracts\Support\Arrayable;

class TimeSchedule implements Arrayable {

	protected $id;
	protected $monday;
	protected $tuesday;
	protected $wednesday;
	protected $thursday;
	protected $friday;
	protected $saturday;
	protected $sunday;
	protected $start_time;
	protected $end_time;
	protected $start_date;
	protected $end_date;

	public function __construct(array $apiResponse = []) {
		$this->id = $apiResponse['id'] ?? null;
		$this->monday = boolval($apiResponse['monday'] ?? false);
		$this->tuesday = boolval($apiResponse['tuesday'] ?? false);
		$this->wednesday = boolval($apiResponse['wednesday'] ?? false);
		$this->thursday = boolval($apiResponse['thursday'] ?? false);
		$this->friday = boolval($apiResponse['friday'] ?? false);
		$this->saturday = boolval($apiResponse['saturday'] ?? false);
		$this->sunday = boolval($apiResponse['sunday'] ?? false);
		$this->start_time = $apiResponse['start_time'] ?? null;
		$this->end_time = $apiResponse['end_time'] ?? null;
		$this->start_date = $apiResponse['start_date'] ?? null;
		$this->end_date = $apiResponse['end_date'] ?? null;
	}

	public function toArray($stripID = false) : array {
		$data = [
			'monday' => $this->monday,
			'tuesday' => $this->tuesday,
			'wednesday' => $this->wednesday,
			'thursday' => $this->thursday,
			'friday' => $this->friday,
			'saturday' => $this->saturday,
			'sunday' => $this->sunday,
			'start_time' => $this->start_time,
			'end_time' => $this->end_time,
			'start_date' => $this->start_date,
			'end_date' => $this->end_date,
		];

		if(!$stripID) {
			$data['id'] = $this->id;
		}

		return $data;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function isMondayEnabled(): bool {
		return $this->monday;
	}

	public function setMondayEnabled(bool $monday): void {
		$this->monday = $monday;
	}

	public function isTuesdayEnabled(): bool {
		return $this->tuesday;
	}

	public function setTuesdayEnabled(bool $tuesday): void {
		$this->tuesday = $tuesday;
	}

	public function isWednesdayEnabled(): bool {
		return $this->wednesday;
	}

	public function setWednesdayEnabled(bool $wednesday): void {
		$this->wednesday = $wednesday;
	}

	public function isThursdayEnabled(): bool {
		return $this->thursday;
	}

	public function setThursdayEnabled(bool $thursday): void {
		$this->thursday = $thursday;
	}

	public function isFridayEnabled(): bool {
		return $this->friday;
	}

	public function setFridayEnabled(bool $friday): void {
		$this->friday = $friday;
	}

	public function isSaturdayEnabled(): bool {
		return $this->saturday;
	}

	public function setSaturdayEnabled(bool $saturday): void {
		$this->saturday = $saturday;
	}

	public function isSundayEnabled(): bool {
		return $this->sunday;
	}

	public function setSundayEnabled(bool $sunday): void {
		$this->sunday = $sunday;
	}

	public function setAllWorkdaysEnabled(bool $workdays) : void {
		$this->monday = $workdays;
		$this->tuesday = $workdays;
		$this->wednesday = $workdays;
		$this->thursday = $workdays;
		$this->friday = $workdays;
	}

	public function setAllWeekendsEnabled(bool $weekends) : void {
		$this->saturday = $weekends;
		$this->sunday = $weekends;
	}

	public function getStartTime(): ?string {
		return $this->start_time;
	}

	public function setStartTime(?string $start_time): void {
		$this->start_time = $start_time;
	}

	public function getEndTime(): ?string {
		return $this->end_time;
	}

	public function setEndTime(?string $end_time): void {
		$this->end_time = $end_time;
	}

	public function getStartDate(): ?string {
		return is_string($this->start_date)
			? substr($this->start_date, 0, 10)
			: null;
	}

	public function setStartDate(?string $start_date): void {
		$this->start_date = $start_date;
	}

	public function getEndDate(): ?string {
		return is_string($this->end_date)
			? substr($this->end_date, 0, 10)
			: null;
	}

	public function setEndDate(?string $end_date): void {
		$this->end_date = $end_date;
	}

	public static function allTime() : TimeSchedule {
		return new TimeSchedule([
			'monday' => true,
			'tuesday' => true,
			'wednesday' => true,
			'thursday' => true,
			'friday' => true,
			'saturday' => true,
			'sunday' => true,
			'start_time' => '00:00:00',
			'end_time' => '23:59:59',
			'start_date' => null,
			'end_date' => null,
		]);
	}

	public static function businessHours($startTime = '08:00:00', $endTime = '18:00:00') : TimeSchedule {
		return new TimeSchedule([
			'monday' => true,
			'tuesday' => true,
			'wednesday' => true,
			'thursday' => true,
			'friday' => true,
			'saturday' => false,
			'sunday' => false,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'start_date' => null,
			'end_date' => null,
		]);
	}

}