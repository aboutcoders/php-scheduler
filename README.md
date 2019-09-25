# abc-scheduler

A PHP library to process schedules base on [Symfony Console Component](https://github.com/symfony/console).

Features:
* Call a processor whenever a schedule is due
* Support for CRON based schedules
* Simple integration by implementing two interfaces

**Note: This project is still in an experimental phase!**

## Getting Started

1. Define a schedule provider by implementing `ProviderInterfacve`.
	
	```php
	namespace Abc\Scheduler;
	
	interface ProviderInterface
	{
		/**
		 * @return string The provider's name, used to bind a provider to processors
		 */
		public function getName(): string;
	
		/**
		 * @param int|null $limit
		 * @param int|null $offset
		 * @return ScheduleInterface[]
		 */
		public function provideSchedules(int $limit = null, int $offset = null): array;
	
		public function save(ScheduleInterface $schedule): void;
	}
	```

2. Define a schedule processor by implementing `ProcessorInterface`.

	```php
	namespace Abc\Scheduler;
	
	/**
	 * Process a schedule that is due.
	 */
	interface ProcessorInterface
	{
		public function process(ScheduleInterface $schedule);
	}
	```

3. Bind Processor to Provider and initialize the ScheduleCommand

	```php
	use Abc\Scheduler\Scheduler;
	use Abc\Scheduler\Symfony\ScheduleCommand;
	
	$myProvider = new MyProvider();
	$myProcessor = new MyProcessor();
	
	$scheduler = new Scheduler();
	$scheduler->bind($myProvider, $myProcessor);
	
	$command = new ScheduleCommand($scheduler);
	```
	
4. Run the command

	```bash
	bin/console abc:schedule
	```

## Todo
- allow multiple processors per schedule

## License

The MIT License (MIT). Please see [License File](./LICENSE) for more information.
