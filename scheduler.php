<?php
// Scheduler extension, https://github.com/adegans/yellow-scheduler

class YellowScheduler {
	const VERSION = "1.1";
	public $yellow;		 // access to API
	public $store;		 // Cache file

	// Handle initialisation
	public function onLoad($yellow) {
		$this->yellow = $yellow;
		$this->store = "system/workers/scheduler.ini";
        $this->yellow->system->setDefault("SchedulerPublishInterval", "6");
	}

    // Handle page meta data
    public function onParseMetaData($page) {
		if($this->yellow->extension->isExisting("blog") AND $this->yellow->extension->isExisting("draft")) {
	        if($page->get("layout")=="blog-start") {
				$interval = trim($this->yellow->toolbox->readFile($this->store));
				if(empty($interval)) {
					$interval = time();
					if(!$this->yellow->toolbox->writeFile($this->store, $interval)) {
						$this->yellow->toolbox->log("error", "Couldn't create config file '".$this->store."'!");
					}
				}

				if($interval < time()) {
					$pages = $page->getChildren(1);
					foreach($pages as $draft) {
						$publishdate = explode(" ", $draft->get("published"));
						$date = explode("-", $publishdate[0]);
						$time = (!empty($publishdate[1])) ? explode(":", $publishdate[1]) : array(0, 0, 0);
						$publishdate = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

						if($publishdate < time()) {
							$fileData = $fileDataNew = $this->yellow->toolbox->readFile($draft->fileName);

							$pos = strpos($fileData, "Status: draft");
							if($pos !== false) {
    							$fileDataNew = substr_replace($fileData, "Status: public", $pos, 13);
							}

							if($fileData != $fileDataNew AND !$this->yellow->toolbox->writeFile($draft->fileName, $fileDataNew)) {
								$this->yellow->toolbox->log("error", "Couldn't publish blog post '".$draft->fileName."'!");
							}
						}

						unset($publishdate, $date, $time, $fileData, $fileDataNew);
					}

                	$interval_hours = $this->yellow->system->get("SchedulerPublishInterval");
                	$interval_hours = (is_numeric($interval_hours)) ? floor($interval_hours) : 6;
					$nextrun = time() + (3600 * $interval_hours);
					if(!$this->yellow->toolbox->writeFile($this->store, $nextrun)) {
						$this->yellow->toolbox->log("error", "Couldn't update cache file '".$this->store."'!");
					}
				}
			}
		} else {
			$this->yellow->toolbox->log("error", "Blog and/or Draft extension is not installed!");
		}
	}
}
