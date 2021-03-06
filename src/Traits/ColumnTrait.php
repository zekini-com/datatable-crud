<?php

namespace Zekini\DatatableCrud\Traits;

use Illuminate\Support\Facades\Schema;

trait ColumnTrait
{
    protected $dontShow = [
        'id',
        'created_at',
        'deleted_at',
        'email_verified_at',
        'password',
        'push_token',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'updated_at',
    ];

    /**
     * Gets column details of table
     *
     * @return \Illuminate\Support\Collection
     */
    public function getColumnDetails()
    {
        $tableName = $this->argument('table');
        $blackList = $this->dontShow;

        $columns = collect(Schema::getColumnListing($tableName));

        return $columns->reject(function ($col) use ($blackList) {
            return in_array($col, $blackList);
        })
            ->map(function ($col) use ($tableName) {
                return [
                    'name' => $col,
                    'type' => Schema::getColumnType($tableName, $col),
                    'required' => boolval(Schema::getConnection()->getDoctrineColumn($tableName, $col)->getNotnull())
                ];
            })
            ->sortBy('name');
    }

    /**
     * Gets column details of table
     *
     * @return \Illuminate\Support\Collection
     */
    public function getColumnWithDates()
    {
        $tableName = $this->argument('table');
        $blackList = $this->dontShow;

        $columns = collect(Schema::getColumnListing($tableName));
        $columns = $columns->reject(function ($col) use ($blackList) {
            return in_array($col, $blackList);
        })
            ->map(function ($col) use ($tableName) {
                return [
                    'name' => $col,
                    'type' => Schema::getColumnType($tableName, $col),
                    'required' => boolval(Schema::getConnection()->getDoctrineColumn($tableName, $col)->getNotnull())
                ];
            });

        return $columns;
    }

    /**
     * Gets column details of table
     *
     * @return array
     */
    public function getColumnDetailsWithRelations()
    {
        $columns = $this->getColumnDetails();

        return $this->belongsToConfiguration()->pluck('column')->toArray();
    }


    /**
     * The model contains a particular column
     *
     * @param  string $col
     * @return boolean
     */
    public function hasColumn($col)
    {
        return Schema::hasColumn($this->argument('table'), $col);
    }

}
