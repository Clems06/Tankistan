<?php

declare(strict_types=1);

final class Noise2D
{
    private array $pointsMod12 = [];
    private array $points = [];
    private float $F2;
    private float $G2;
    private float $persistence;
    private int $octaves;
    private float $zoom;
    private float $elevation;

    public function __construct(
        float $zoom = 0.025,
        int $octaves = 4,
        float $persistence = 0.5,
        float $elevation = 0.0
    ) {
        $this->persistence = $persistence;
        $this->octaves = $octaves;
        $this->zoom = $zoom;
        $this->elevation = $elevation;
        for ($i = 0; $i < 512; $i++) {
            //$value = NoiseData::PSEUDO_RANDOM_POINTS[$i & 255];
            $value = random_int(0, 255);
            $this->points[$i] = $value;
            $this->pointsMod12[$i] = $value % 12;
        }

        $this->F2 = 0.5 * (sqrt(3.0) - 1.0);
        $this->G2 = (3.0 - sqrt(3.0)) / 6.0;
    }

    private function fastFloor(float $value): int
    {
        $intValue = (int)$value;
        return $value < $intValue ? $intValue - 1 : $intValue;
    }

    public function getGreyValue(int $x, int $y): int
    {
        $x = $x * $this->zoom;
        $y = $y * $this->zoom;
        $noise = $this->fbm($x, $y);

        return $this->fastFloor($this->interpolate(0, 255, $noise));
    }

    public function interpolate(float $x, float $y, float $alpha)
    {
        if ($alpha < 0.0) {
            return $x;
        }
        if ($alpha > 1.0) {
            return $y;
        }
        return (1 - $alpha) * $x + $alpha * $y;
    }

    public function noise(float $x, float $y): float
    {
        $s = ($x + $y) * $this->F2;

        $i = $this->fastFloor($x + $s);
        $j = $this->fastFloor($y + $s);

        $t = ($i + $j) * $this->G2;

        $x00 = $i - $t;
        $y00 = $j - $t;

        $x0 = $x - $x00;
        $y0 = $y - $y00;

        $i1 = 0;
        $j1 = 1;
        if ($x0 > $y0) {
            $i1 = 1;
            $j1 = 0;
        }

        $x1 = $x0 - $i1 + $this->G2;
        $y1 = $y0 - $j1 + $this->G2;

        $x2 = $x0 - 1.0 + 2.0 * $this->G2;
        $y2 = $y0 - 1.0 + 2.0 * $this->G2;

        $ii = $i & 255;
        $jj = $j & 255;

        $index0 = $ii + $this->points[$jj];
        $index1 = $ii + $i1 + $this->points[$jj + $j1];
        $index2 = $ii + 1 + $this->points[$jj + 1];

        $gi0 = (int)$this->pointsMod12[$index0];
        $gi1 = (int)$this->pointsMod12[$index1];
        $gi2 = (int)$this->pointsMod12[$index2];

        $n0 = $this->getN($x0, $y0, NoiseData::GRADIENT_MAP[$gi0]);
        $n1 = $this->getN($x1, $y1, NoiseData::GRADIENT_MAP[$gi1]);
        $n2 = $this->getN($x2, $y2, NoiseData::GRADIENT_MAP[$gi2]);

        return 70.0 * ($n0 + $n1 + $n2);
    }

    public function fbm(float $x, float $y): float
    {
        $value = $this->elevation;

        $maxValue = $this->elevation;
        $frequency = 1.0;
        $amplitude = 1.0;
        $persistence = $this->persistence;

        for ($octave = 0; $octave < $this->octaves; $octave++) {
            $value += $this->noise($x * $frequency, $y * $frequency) * $amplitude;
            $maxValue += $amplitude;
            $amplitude *= $persistence;
            $frequency *= 2.0;
        }
        return $value / $maxValue;
    }


    private function dot(array $points, float $x, float $y): float
    {
        return $points[0] * $x + $points[1] * $y;
    }

    private function getN(float $x, float $y, array $gradient): float
    {
        $t = (0.5 - $x * $x - $y * $y);
        if ($t < 0) {
            return 0.0;
        }
        $t *= $t;
        return $t * $t * $this->dot($gradient, $x, $y);
    }

}




final class NoiseData
{
    public const GRADIENT_MAP = [
        [1.0, 1.0, 0.0],
        [-1.0, 1.0, 0.0],
        [1.0, -1.0, 0.0],
        [-1.0, -1.0, 0.0],
        [1.0, 0.0, 1.0],
        [-1.0, 0.0, 1.0],
        [1.0, 0.0, -1.0],
        [-1.0, 0.0, -1.0],
        [0.0, 1.0, 1.0],
        [0.0, -1.0, 1.0],
        [0.0, 1.0, -1.0],
        [0.0, -1.0, -1.0]
    ];

}

?>