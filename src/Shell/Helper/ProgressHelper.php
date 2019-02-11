<?php
namespace ButterCream\Shell\Helper;

use Cake\Shell\Helper\ProgressHelper as CakeProgressHelper;

class ProgressHelper extends CakeProgressHelper
{

    /**
     * Getter method to expose progress and total
     *
     * @param string $key - The object property
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'progress') {
            return $this->_progress;
        } elseif ($key === 'total') {
            return $this->_total;
        } elseif ($key === 'complete') {
            $complete = round($this->_progress / $this->_total, 2);
            if ($complete === (float)1 && $this->_progress < $this->_total) {
                $complete = .99;
            }

            return $complete;
        } elseif ($key === 'percent') {
            return ($this->complete * 100) . '%';
        }
    }

    /**
     * Render the progress bar based on the current state.
     *
     * @return void
     */
    public function draw()
    {
        $numberLen = strlen(' 100%');

        $complete = $this->complete;
        $barLen = ($this->_width - $numberLen) * ($this->_progress / $this->_total);
        $bar = '';
        if ($barLen > 1) {
            $bar = str_repeat('=', $barLen - 1) . '>';
        }

        $pad = ceil($this->_width - $numberLen - $barLen);
        if ($pad > 0) {
            $bar .= str_repeat(' ', $pad);
        }
        $percent = ($complete * 100) . '%';
        $bar .= str_pad($percent, $numberLen, ' ', STR_PAD_LEFT);

        $this->_io->overwrite($bar, 0);
    }
}
