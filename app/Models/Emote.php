<?php

namespace App\Models;

class Emote extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'alt_text', 'is_active',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emotes';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'  => 'required|unique:items|between:3,100',
        'image' => 'required|mimes:png,jpg,jpeg,gif,apng,webp',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'  => 'required|between:3,100',
        'image' => 'mimes:png,jpg,jpeg,gif,apng,webp',
    ];

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to retrieve only active emotes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('is_active', 1);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the emote in the tinymce editor.
     *
     * @return string
     */
    public function getMentionImageAttribute() {
        return '<span data-mention-type="emote" data-id="'.$this->id.'"><img src="'.$this->imageUrl.'" alt="'.$this->alt_text.'" class="img-fluid rounded" data-toggle="tooltip" title="'.$this->name.' - '.$this->alt_text.'"></span>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/emotes';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute() {
        return $this->id.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute() {
        return asset($this->imageDirectory.'/'.$this->imageFileName);
    }

    /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/emotes/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     *
     * @return string
     */
    public function getAdminPowerAttribute() {
        return 'edit_data';
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Returns the emote's image as an HTML image element with alt text.
     *
     * @return string
     */
    public function getImage() {
        return '<img src="'.$this->imageUrl.'" alt="'.$this->alt_text.'" data-toggle="tooltip" title="'.$this->name.' - '.$this->alt_text.'">';
    }
}
