<?php

namespace App\Service\Aramex;

class AramexTrackingEntity
{

    private $WaybillNumber;

    private $UpdateCode;
 
    private $UpdateDescription;

    private $UpdateDateTime;

    private $UpdateLocation;

    private $Comments;

    private $ProblemCode;

    private $GrossWeight;

    private $ChargeableWeight;

    private $WeightUnit;


    

    /**
     * Get the value of WaybillNumber
     */ 
    public function getWaybillNumber(): string
    {
        return $this->WaybillNumber;
    }

    /**
     * Set the value of WaybillNumber
     *
     * @return  self
     */ 
    public function setWaybillNumber(string $WaybillNumber)
    {
        $this->WaybillNumber = $WaybillNumber;

        return $this;
    }

    /**
     * Get the value of UpdateCode
     */ 
    public function getUpdateCode(): string
    {
        return $this->UpdateCode;
    }

    /**
     * Set the value of UpdateCode
     *
     * @return  self
     */ 
    public function setUpdateCode($UpdateCode)
    {
        $this->UpdateCode = $UpdateCode;

        return $this;
    }

    /**
     * Get the value of UpdateDescription
     */ 
    public function getUpdateDescription()
    {
        return $this->UpdateDescription;
    }

    /**
     * Set the value of UpdateDescription
     *
     * @return  self
     */ 
    public function setUpdateDescription($UpdateDescription)
    {
        $this->UpdateDescription = $UpdateDescription;

        return $this;
    }

    /**
     * Get the value of UpdateDateTime
     */ 
    public function getUpdateDateTime()
    {
        return $this->UpdateDateTime;
    }

    /**
     * Set the value of UpdateDateTime
     *
     * @return  self
     */ 
    public function setUpdateDateTime($UpdateDateTime)
    {
        $this->UpdateDateTime = $UpdateDateTime;

        return $this;
    }

    /**
     * Get the value of UpdateLocation
     */ 
    public function getUpdateLocation()
    {
        return $this->UpdateLocation;
    }

    /**
     * Set the value of UpdateLocation
     *
     * @return  self
     */ 
    public function setUpdateLocation($UpdateLocation)
    {
        $this->UpdateLocation = $UpdateLocation;

        return $this;
    }

    /**
     * Get the value of Comments
     */ 
    public function getComments()
    {
        return $this->Comments;
    }

    /**
     * Set the value of Comments
     *
     * @return  self
     */ 
    public function setComments($Comments)
    {
        $this->Comments = $Comments;

        return $this;
    }

    /**
     * Get the value of ProblemCode
     */ 
    public function getProblemCode()
    {
        return $this->ProblemCode;
    }

    /**
     * Set the value of ProblemCode
     *
     * @return  self
     */ 
    public function setProblemCode($ProblemCode)
    {
        $this->ProblemCode = $ProblemCode;

        return $this;
    }

    /**
     * Get the value of GrossWeight
     */ 
    public function getGrossWeight()
    {
        return $this->GrossWeight;
    }

    /**
     * Set the value of GrossWeight
     *
     * @return  self
     */ 
    public function setGrossWeight($GrossWeight)
    {
        $this->GrossWeight = $GrossWeight;

        return $this;
    }

    /**
     * Get the value of ChargeableWeight
     */ 
    public function getChargeableWeight()
    {
        return $this->ChargeableWeight;
    }

    /**
     * Set the value of ChargeableWeight
     *
     * @return  self
     */ 
    public function setChargeableWeight($ChargeableWeight)
    {
        $this->ChargeableWeight = $ChargeableWeight;

        return $this;
    }

    /**
     * Get the value of WeightUnit
     */ 
    public function getWeightUnit()
    {
        return $this->WeightUnit;
    }

    /**
     * Set the value of WeightUnit
     *
     * @return  self
     */ 
    public function setWeightUnit($WeightUnit)
    {
        $this->WeightUnit = $WeightUnit;

        return $this;
    }
}