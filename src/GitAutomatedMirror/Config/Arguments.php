<?php # -*- coding: utf-8 -*-

namespace GitAutomatedMirror\Config;
use GitAutomatedMirror\Common;
use GitAutomatedMirror\Type;
use GetOptionKit;

class Arguments {

	/**
	 * @type GetOptionKit\OptionCollection
	 */
	private $optionCollection;

	/**
	 * @type Common\ApplicationArgumentBuilder
	 */
	private $argumentBuilder;

	/**
	 * @type array
	 */
	private $definedArguments = [];

	/**
	 * @param GetOptionKit\OptionCollection   $optionCollection
	 * @param Common\ApplicationArgumentBuilder $argumentBuilder
	 */
	public function __construct(
		GetOptionKit\OptionCollection $optionCollection,
		Common\ApplicationArgumentBuilder $argumentBuilder
	) {

		$this->optionCollection = $optionCollection;
		$this->argumentBuilder  = $argumentBuilder;
		$this->createArguments();
	}

	/**
	 * register the application arguments
	 *
	 */
	public function registerDefaultArguments() {

		foreach ( $this->definedArguments as $arg ) {
			/* @type Type\ApplicationArgument $arg */
			$combinedName = $this->getCombinedName( $arg );
			$this->optionCollection->add( $combinedName )
				->isa( $arg->getType() )
				->desc( $arg->getDescription() );
		}
	}

	/**
	 * provide the structure for each
	 * of the application arguments
	 */
	private function createArguments() {

		$definedArgumentsStructure = [
			[
				'name'        => 'help',
				'type'        => 'string',
				'isRequired'  => FALSE,
				'shortName'   => 'h',
				'description' => 'Print this help message.'
			],
			[
				'name'        => 'dir',
				'type'        => 'string',
				'isRequired'  => TRUE,
				'shortName'   => 'd',
				'description' => 'Directory of the git repository.'
			],
			[
				'name'        => 'remote-source',
				'type'        => 'string',
				'isRequired'  => TRUE,
				'shortName'   => '',
				'description' => 'Remote of the source repo. Typically "origin".'
			],
			[
				'name'        => 'remote-mirror',
				'type'        => 'string',
				'isRequired'  => TRUE,
				'shortName'   => '',
				'description' => 'Remote of the mirror repo.'
			],
		];

		foreach ( $definedArgumentsStructure as $arg ) {
			$this->definedArguments[ $arg[ 'name' ] ] = $this->argumentBuilder->buildArgument(
				$arg[ 'name' ],
				$arg[ 'type' ],
				$arg[ 'isRequired' ],
				$arg[ 'shortName' ],
				$arg[ 'description' ]
			);
		}
	}

	/**
	 * Builds the combined identifier for the GetOptionKit\OptionCollection::add() method
	 *
	 * A combined name looks like h|help?string
	 *
	 * @see GetOptionKit\Option::initFromSpecString
	 * @param Type\ApplicationArgument $arg
	 * @return string
	 */
	public function getCombinedName( Type\ApplicationArgument $arg ) {

		$name = $arg->getName();
		if ( $arg->getShortName() )
			$name = $arg->getShortName() . '|' . $name;

		if ( $arg->isRequired() )
			$name .= ':';

		return $name;
	}
} 