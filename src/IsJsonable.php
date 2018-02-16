<?php
/**
 * Created on 14/02/18 by enea dhack.
 */

namespace Enea\Authorization;

trait IsJsonable
{
    /**
     * {@inheritdoc}
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
