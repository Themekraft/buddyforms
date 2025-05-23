<?php

require '.tk/RoboFileBase.php';

class RoboFile extends RoboFileBase {

	public function directoriesStructure() {
		return array( 'assets', 'includes', 'languages', 'templates', 'vendor-scope' );
	}

	public function fileStructure() {
		return array( 'BuddyForms.php', 'composer.json', 'license.txt', 'loco.xml', 'readme.txt', 'vendor-scope/buddyforms/composer.json' );
	}

	/**
	 * @return array List of relative paths from the root folder of the plugin
	 */
	public function cleanPhpDirectories() {
		return array( 'assets', 'includes/resources/freemius', 'vendor-scope' );
	}

	public function pluginMainFile() {
		return 'BuddyForms';
	}

	public function pluginFreemiusId() {
		return 391;
	}

	public function minifyImagesDirectories() {
		return array();
	}

	public function minifyAssetsDirectories() {
		return array( 'assets' );
	}

	/**
	 * @return array Pair list of sass source directory and css target directory
	 */
	public function sassSourceTarget() {
		return array( array( 'scss/source' => 'assets/css' ) );
	}

	/**
	 * @return string Relative paths from the root folder of the plugin
	 */
	public function sassLibraryDirectory() {
		return 'scss/library';
	}

	/**
	 * Update git submodules and commit changes if any.
	 */
	public function submodulesUpdate() {
		$submodule_url  = 'git@github.com:Themekraft/pricing-page.git';
		$submodule_path = 'admin/pricing-page';

		$this->say( 'Starting submodule update process...' );

		// Check if submodule already exists.
		$submodule_exists = $this->taskExec( 'git submodule status' )
			->printOutput( false )
			->run()
			->getMessage();

		$is_submodule_present = false !== strpos( $submodule_exists, $submodule_path );

		if ( ! $is_submodule_present ) {
			// Add submodule if it doesn't exist.
			$this->say( "Adding new submodule: {$submodule_url} -> {$submodule_path}" );

			$result = $this->taskExec( "git submodule add --force {$submodule_url} {$submodule_path}" )
				->run();

			if ( ! $result->wasSuccessful() ) {
				$this->say( 'Failed to add submodule!' );
				return;
			}

			$this->say( 'Submodule added successfully!' );
		} else {
			$this->say( 'Submodule already exists, proceeding with update...' );
		}

		// Initialize and update submodules.
		$this->say( 'Initializing and updating submodules...' );

		$init_result = $this->taskExec( 'git submodule init' )
			->run();

		if ( ! $init_result->wasSuccessful() ) {
			$this->say( 'Failed to initialize submodules!' );
			return;
		}

		$update_result = $this->taskExec( 'git submodule update --remote --merge' )
			->run();

		if ( ! $update_result->wasSuccessful() ) {
			$this->say( 'Failed to update submodules!' );
			return;
		}

		$this->say( 'Submodules updated successfully!' );

		// Check if there are any changes to commit.
		$status_result = $this->taskExec( 'git status --porcelain' )
			->printOutput( false )
			->run();

		$changes = trim( $status_result->getMessage() );

		if ( ! empty( $changes ) ) {
			$this->say( 'Changes detected, committing...' );

			// Add all changes.
			$add_result = $this->taskExec( 'git add .' )
				->run();

			if ( ! $add_result->wasSuccessful() ) {
				$this->say( 'Failed to add changes!' );
				return;
			}

			// Commit changes.
			$commit_message = "Update submodule: {$submodule_path}";
			$commit_result  = $this->taskExec( "git commit -m \"{$commit_message}\"" )
				->run();

			if ( $commit_result->wasSuccessful() ) {
				$this->say( 'Changes committed successfully!' );
				$this->say( "Commit message: {$commit_message}" );
			} else {
				$this->say( 'Failed to commit changes!' );
			}
		} else {
			$this->say( 'No changes detected, nothing to commit.' );
		}

		$this->say( 'Submodule update process completed!' );
	}
}
