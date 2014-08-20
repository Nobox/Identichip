<?php
namespace Nobox\Identichip\Models;


Class Service extends \Eloquent {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'services';

    protected $fillable = array(
        'service_id',
        'name',
    );
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('User');
    }
}
