<?php

class Parser
{
    /**
     * Program entrance
     */
    public function run($argc, $argv)
    {
        $arguments = [];

        if (isset($argc)) {
            for ($i = 0; $i < $argc; $i++) {
                $arguments[$i] = $argv[$i];
            }

            $data = self::processArguments($arguments);

            $file = self::loadFile($data['get_file']);

            self::displayResult($file);

            self::saveResult($file, $data['action_type'], $data['save_file'], $data['file_format']);
        }
    }

    /**
     * Strip arguments and return array
     */
    public static function processArguments(array $arguments)
    {
        $requestedData = [];
        if (preg_match("/^[-]{2}file/", $arguments[1] . $arguments[2])) {
            $requestedData['save_file'] = $arguments[2];
            $requestedData['file_format'] = substr($arguments[2], strrpos($arguments[2], '.') + 1);
        }

        if (preg_match("/^[-]{2}[a-z]/", $arguments[3])) {
            $requestedData['get_file'] = substr($arguments[3], strpos($arguments[3], "=") + 1);
            $requestedData['action_type'] = substr($arguments[3], 0, strpos($arguments[3], "="));
        }

        return $requestedData;
    }

    /**
     * Specify file location and loop though all values
     */
    public static function loadFile(string $file)
    {
        $allLines = [];

        foreach (self::getLines('files/input/' . $file) as $n => $line) {
            $allLines[$n] = $line;
        }

        return $allLines;
    }

    /**
     * Get line by line file content, if file not found throw error
     */
    public static function getLines(string $fileName)
    {
        $file = fopen($fileName, 'r');

        if ($file) {

            try {
                while ($line = fgets($file)) {
                    yield $line;
                }
            } finally {
                fclose($file);
            }

        } else {
            throw new Exception("File not found.");
        }
    }

    /**
     * Display results in terminal
     */
    public static function displayResult(array $file)
    {
        $preparedArray = [];

        $i = 0;

        foreach ($file as $val) {
            if (preg_match_all("/[\"']{1}(.*?)[\"']{1}/im", $val, $v, PREG_SET_ORDER)) {
                $preparedArray[$i++] = [
                    'make' => $v[0][1] ?: self::exception('make'),
                    'model' => $v[1][1] ?: self::exception('model'),
                    'colour' => $v[2][1] ?? '',
                    'capacity' => $v[3][1] ?? '',
                    'network' => $v[4][1] ?? '',
                    'grade' => $v[5][1] ?? '',
                    'condition' => $v[6][1] ?? '',
                ];
            }
        }
        print_r($preparedArray);
    }

    /**
     * Save result
     */
    public static function saveResult(array $file, string $action, string $newFileName, string $fileFortmat)
    {
        if ($action === '--unique-combinations' && $fileFortmat === 'csv') {
            $vals = array_count_values($file);

            $allValues = [];

            $i = 0;

            foreach ($vals as $key => $val) {
                if (preg_match_all("/[\"']{1}(.*?)[\"']{1}/im", $key, $v, PREG_SET_ORDER)) {
                    $allValues[$i++] = [
                        'make' => $v[0][1] ?: self::exception('make'),
                        'model' => $v[1][1] ?: self::exception('model'),
                        'colour' => $v[2][1] ?? '',
                        'capacity' => $v[3][1] ?? '',
                        'network' => $v[4][1] ?? '',
                        'grade' => $v[5][1] ?? '',
                        'condition' => $v[6][1] ?? '',
                        'count' => $val,
                    ];
                }
                $stringValues = [];
                foreach ($allValues as $value) {
                    $stringValues[$i++] = 'make: ' . $value['make'] . ' model:' . $value['model'] . ' colour:' . $value['colour'] . ' capacity: ' . $value['capacity'] . ' network: ' . $value['network'] . ' grade' . $value['grade'] . ' condition: ' . $value['condition'] . ' count: ' . $value['count'] . "\n";
                }
                file_put_contents('files/output/' . $newFileName, $stringValues);
            }
        } else {
            throw new Exception("Unsupported file format/param");
        }
    }

    /**
     * Exception if value is empty
     */
    public static function exception($field)
    {
        throw new Exception("Field $field is empty.");
    }
}
