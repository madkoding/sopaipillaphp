<?php

namespace Supernova;

class Model extends \Supernova\Sql\Query implements \Iterator
{
    use \Supernova\Model\Iterator;
    use \Supernova\Model\Base;
    use \Supernova\Model\Magic;
    use \Supernova\Model\Process;
    use \Supernova\Model\Relations;
}
