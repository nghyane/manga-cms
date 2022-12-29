<?php

namespace App\Helpers;


/**
 * Class TaskRunner
 * Exemple d'utilisation:
 * ====================
 * $task = new TaskRunner();
 * $task->runCommand("php artisan queue:work --tries=3 --timeout=90 --sleep=3 --daemon");
 * ====================
 * @package App\Helpers
 *
 */

class TaskRunner
{
    private $output;
    private $error;
    private $running = false;
    public $command_prefix = '';

    public function __construct($command_prefix = '')
    {
        if (is_string($command_prefix)) {
            $this->command_prefix = trim($command_prefix) . " ";
        }
    }

    public function runCommandInBackground($command)
    {
        // Check if bash is available
        $bashExists = $this->checkBashAvailability();
        $command = $this->command_prefix . $command;

        if ($bashExists) {
            // Use proc_open to run the command in the background
            $this->runBashCommandInBackground($command);
        } else {
            // Use an alternative method to run the command in the background
            $this->runCommandWithAlternativeMethod($command, true);
        }

        // Set the running flag to true
        $this->running = true;

        return $this;
    }

    public function runCommandInForeground($command)
    {
        // Check if bash is available
        $bashExists = $this->checkBashAvailability();
        $command = $this->command_prefix . $command;

        if ($bashExists) {
            // Use proc_open to run the command in the foreground
            $this->runBashCommandInForeground($command);
        } else {
            // Use an alternative method to run the command in the foreground
            $this->runCommandWithAlternativeMethod($command, false);
        }

        // Set the running flag to true
        $this->running = true;

        return $this;
    }

    public function checkBashAvailability()
    {
        exec("bash --version", $output, $error);
        return $error === 0;
    }

    private function runBashCommandInBackground($command)
    {
        // Create a pipe for reading and writing
        $descriptors = [
            0 => ["pipe", "r"],  // stdin is a pipe that the child will read from
            1 => ["pipe", "w"],  // stdout is a pipe that the child will write to
            2 => ["pipe", "w"]   // stderr is a pipe that the child will write to
        ];

        // Start the process
        $process = proc_open('bash', $descriptors, $pipes);

        if (is_resource($process)) {
            // Write the command to the stdin pipe
            fwrite($pipes[0], $command . "\n");
            fclose($pipes[0]);

            // Read the output and error from the stdout and stderr pipes
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);

            // Close the pipes and the process
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            // Save the output and error
            $this->output = $output;
            $this->error = $error;
        }
    }

    private function runBashCommandInForeground($command)
    {
        // Create a pipe for reading and writing
        $descriptors = [
            0 => ["pipe", "r"],  // stdin is a pipe that the child will read from
            1 => ["pipe", "w"],  // stdout is a pipe that the child will write to
            2 => ["pipe", "w"]   // stderr is a pipe that the child will write to
        ];

        // Start the process
        $process = proc_open('bash', $descriptors, $pipes);

        if (is_resource($process)) {
            // Write the command to the stdin pipe
            fwrite($pipes[0], $command . "\n");
            fclose($pipes[0]);

            // Read the output and error from the stdout and stderr pipes
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);

            // Close the pipes and the process
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            // Save the output and error
            $this->output = $output;
            $this->error = $error;
        }
    }

    private function runCommandWithAlternativeMethod($command, $background)
    {
        // Detect the operating system
        $uname = strtoupper(php_uname("s"));

        if (strpos($uname, "WIN") !== false) {
            // Windows
            if ($background) {
                exec("start /B " . $command, $output, $error);
            } else {
                exec($command, $output, $error);
            }
        } else {
            // Unix-like (Linux, macOS, etc.)
            if ($background) {
                exec($command . " > /dev/null &", $output, $error);
            } else {
                exec($command, $output, $error);
            }
        }

        // Save the output and error
        $this->output = implode("\n", $output);
        $this->error = $error;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getError()
    {
        return $this->error;
    }

    public function isRunning()
    {
        return $this->running;
    }
}
