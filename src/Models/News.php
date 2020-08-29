<?php

namespace Xmen\StarterKit\Models;

use Xmen\StarterKit\Helpers\TDate;
use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Te7aHoudini\LaravelTrix\Traits\HasTrixRichText;


/**
 * App\News
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $subtitle
 * @property string $body
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News newQuery()
 * @method static \Illuminate\Database\Query\Builder|\Xmen\StarterKit\Models\News onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Xmen\StarterKit\Models\News withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Xmen\StarterKit\Models\News withoutTrashed()
 * @mixin \Eloquent
 * @property int $user_id
 * @property int $is_breaking
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereIsBreaking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereUserId($value)
 * @property int $status
 * @property array $tag_names
 * @property-read \Illuminate\Database\Eloquent\Collection|\Tagged[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\Conner\Tagging\Model\Tagged[] $tagged
 * @property-read int|null $tagged_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News withAllTags($tagNames)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News withAnyTag($tagNames)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News withoutTags($tagNames)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Xmen\StarterKit\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Te7aHoudini\LaravelTrix\Models\TrixAttachment[] $trixAttachments
 * @property-read int|null $trix_attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Te7aHoudini\LaravelTrix\Models\TrixRichText[] $trixRichText
 * @property-read int|null $trix_rich_text_count
 * @property string $hash
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereHash($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Xmen\StarterKit\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Xmen\StarterKit\Models\Comment[] $approved_comments
 * @property-read int|null $approved_comments_count
 * @property int $is_pinned
 * @property int $like
 * @property int $dislike
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereDislike($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Xmen\StarterKit\Models\News whereLike($value)
 */
class News extends Model implements HasMedia {
    use SoftDeletes, InteractsWithMedia, Taggable, HasTrixRichText;


    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    //
    public function getRouteKeyName() {
        return 'slug';
    }


    public function registerMediaConversions(Media $media = null): void {

        $this->addMediaConversion('news-image')
            ->width(1200)
            ->height(600)
            ->crop(Manipulations::CROP_CENTER, 1200, 600)
            ->optimize()
            ->sharpen(10);

//        $this->addMediaConversion('pgallery')->width(1200)->optimize();
        //            ->watermark(public_path('images/logo.png'))->watermarkOpacity(50);
        //            ->withResponsiveImages();
    }

    public function imgurl() {
        if ($this->getMedia()->count() > 0) {
            return $this->getMedia()->first()->getUrl('news-image');
        } else {
            return "no image";
        }
    }

    public function spendTime() {

        $word = strlen(strip_tags($this->body));
        $m = ceil($word / 1350);
//        $est = $m . ' '.__('minute') . ($m == 1 ? '' : 's') . ', ' . $s . ' second' . ($s == 1 ? '' : 's');
        return $m . ' ' . __('minute') ;
    }

    public function persianDate(){
        $dt = TDate::GetInstance();
        return $dt->PDate("Y/m/d H:i:s",$this->created_at->timestamp);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function approved_comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->where('status',1);
    }


}
