<?php

/**
 * Class works with folder. Client should call function @scanFolder and pass there two parameters:
 * @dir - path to directory (if empty library gets current project path)
 * @type - in witch format client want get information json or array, by default is array
 * If client passes incorrect data, he sees error
 *
 * Author: Oleksandr Paiziak
 */

class Scanner
{
    /**
     * Call necessary function and prepare result to correct form
     *
     * @param string $dir
     * @param string $type
     *
     * @return array
     * return json or array if user passes correct type, else it returns error
     */

    public function scanFolder($dir = '', $type = 'array') {
        // set default directory to current
        if ($dir == '')
            $dir = getcwd();

        // check if path is correct
        if (!is_dir($dir))
            return 'Directive is not correct';

        $result = $this->dirToArray($dir);

        // check passed parameters
        if ($type == 'array')
            return $result;
        elseif ($type == 'json')
            return json_encode($result);
        else
            return 'Type is not correct, pls, check it.';
    }

    /**
     * Get all files and directive from main folder (recursive)
     *
     * @param string $dir
     *
     * @return array
     */

    function dirToArray($dir = '') {
        $result = array();
        $cdir = scandir($dir);
        // check all subdirectories, and get necessary information
        foreach ($cdir as $key => $value)
        {
            if (in_array($value, array(".","..")))
                continue;

            if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
            {
                $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                $result[$value]['_properties'] = $this->folderInformation($dir . DIRECTORY_SEPARATOR . $value);
            }
        }

        // Sort directories
        $size = array();
        foreach ($result as $key => $directory)
        {
            $size[$key] = $directory['_properties']['size'];
        }
        array_multisort($size, SORT_ASC, $result);

        return $result;
    }

    /**
     * Get information about directive (recursive), save total site of directory and number of files
     *
     * @param string $dir
     *
     * @return array
     */

    function folderInformation ($dir)
    {
        $properties = array('size' => 0, 'count' => 0);
        $files = array();
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $file)
        {
            if (is_file($file))
            {
                $properties['size'] += filesize($file);
                $properties['count'] ++;
                $files[] = $file;
            }
            else
            {
                $subProperties = $this->folderInformation($file);
                $properties['size'] += $subProperties['size'];
                $properties['count'] += $subProperties['count'];
            }
        }
        $properties['duplication'] = $this->filesDuplication($files);

        return $properties;
    }

    /**
     * Get duplication files
     *
     * @param array $files
     *
     * @return Integer
     */

    function filesDuplication($files) {
        $sizeOfFiles = $result = array();
        foreach($files as $key => $file)
        {
            $sizeOfFiles[$key] = filesize($file);
        }

        $duplication = array_count_values($sizeOfFiles);
        foreach($duplication as $key => $dupl)
        {
            if ($dupl > 1)
            {
                $result = $this->compareFile($files, $sizeOfFiles, $key);
            }
        }

        return count($result) > 0 ? $result : 0;
    }

    /**
     * Compare files with the same size
     *
     * @param array $files
     * @param array $sizeOfFiles
     *
     * @return Integer number of the same files in directory
     */
    function compareFile($files, $sizeOfFiles, $size) {
        $result = array();
        $count = 0;

        foreach($sizeOfFiles as $key => $file)
        {
            if ($file == $size)
            {
                $result[] = file_get_contents($files[$key], NULL);
            }
        }

        $duplication = array_count_values($result);
        foreach($duplication as $d)
        {
            $count += $d > 1 ? $d : 0;
        }
        return $count;
    }
}