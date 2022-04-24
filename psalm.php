<?xml version="1.0"?>
<psalm
    errorLevel="3"
    resolveFromConfigFile="true"
    findUnusedVariablesAndParams="true"
    ensureArrayStringOffsetsExist="true"
    ensureArrayIntOffsetsExist="true"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns="https://getpsalm.org/schema/config"
    xsi:schemaLocation="https://getpsalm.org/schema/config vendor/vimeo/psalm/config.xsd"
>
    <projectFiles>
        <directory name="src" />
        <ignoreFiles>
            <directory name="vendor" />
        </ignoreFiles>
    </projectFiles>
</psalm>
