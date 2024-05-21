<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class UniqueInt {
    private $seen;
    private $min_int;

    public function __construct() {
        $this->seen = array_fill(0, 2047, false); 
        $this->min_int = -1023;
    }

    public function process_file($input_file_path, $output_file_path) {
        $this->seen = array_fill(0, 2047, false);
        $unique_numbers = $this->read_unique_integers($input_file_path);
        $this->write_unique_integers($unique_numbers, $output_file_path);
    }

    private function read_unique_integers($input_file_path) {
        $unique_numbers = [];
        $input_file = fopen($input_file_path, 'r'); 
        if ($input_file) {
            while (!feof($input_file)) {
                $line = trim(fgets($input_file)); 
                if (!empty($line) && $this->is_valid_integer_line($line)) {
                    $number = intval($line); 
                    if (-1023 <= $number && $number <= 1023) { 
                        if (!$this->seen[$number - $this->min_int]) {
                            $this->seen[$number - $this->min_int] = true;
                            $unique_numbers[] = $number; 
                        }
                    } else {
                        echo "Number out of range: $number\n"; 
                    }
                }
            }
            fclose($input_file); 
        } else {
            echo "Failed to open input file: $input_file_path\n";
        }
        sort($unique_numbers);
        return $unique_numbers;
    }

    private function is_valid_integer_line($line) {
        if (is_numeric($line)) {
            return true;
        } else {
            echo "Invalid integer line: $line\n"; 
            return false;
        }
    }

    private function write_unique_integers($unique_numbers, $output_file_path) {
        $output_file = fopen($output_file_path, 'w'); 
        if ($output_file) {
            foreach ($unique_numbers as $number) {
                fwrite($output_file, "$number\n"); 
            }
            fclose($output_file); 
        } else {
            echo "Failed to open output file: $output_file_path\n"; 
        }
    }
}

// Main execution
$input_folder = __DIR__ . "/sample_inputs";
$output_folder = __DIR__ . "/sample_results";

echo "Starting processing...\n";
echo "Input folder: $input_folder\n";
echo "Output folder: $output_folder\n";

// Check if input folder exists
if (!is_dir($input_folder)) {
    echo "Input folder does not exist. Please create the folder and add some .txt files.\n";
    exit(1);
}

// Create output folder in case it doesn't exist
if (!is_dir($output_folder)) {
    echo "Output folder does not exist. Creating the folder.\n";
    mkdir($output_folder, 0777, true);
}

$unique_int_processor = new UniqueInt(); //UniqueInt inst

foreach (scandir($input_folder) as $filename) {
    echo "Checking file: $filename\n";
    if (pathinfo($filename, PATHINFO_EXTENSION) === 'txt') { // Sauf .txt documents
        echo "Processing file: $filename\n";
        $input_path = $input_folder . "/" . $filename;
        $output_path = $output_folder . "/" . $filename . "_results.txt";

        $start_time = microtime(true);
        $unique_int_processor->process_file($input_path, $output_path);
        $end_time = microtime(true);

        echo "Processed $filename in " . number_format($end_time - $start_time, 4) . " seconds\n";
    }
}

echo "Processing complete.\n";
?>
