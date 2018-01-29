<?php

// TODO: Move out of this repo, this is a playground test unrelated to this project
class ToStringTest
{
    public function run()
    {
        $this->assertObjectIsCastToStringWhenUsedInString(Object::STRING);
        $this->assertObjectIsCastToStringWhenUsedInString(new Object());
        $this->assertVarDumpsObjectNotString(new Object());
    }

    private function assertObjectIsCastToStringWhenUsedInString($actual)
    {
        $str = "".$actual."";
        echo "Is treated as string\n";
    }

    private function assertVarDumpsObjectNotString($actual)
    {
        ob_start();
        var_dump($actual);
        $result = ob_get_clean();

        if (strpos($result, Object::STRING)) {
            throw new Exception("Error: Is a string");
        }

        echo "vardumped as object successfully\n";
    }
}

class Object
{
    const STRING = "Object as a string";

    public function __toString()
    {
        return self::STRING;
    }
}

class ObjectWithCastings
{

}

(new ToStringTest())->run();