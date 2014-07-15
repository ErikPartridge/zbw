<?php  namespace Zbw\Cms;

use Zbw\Base\EloquentRepository;
use Zbw\Cms\Contracts\PagesRepositoryInterface;
use Zbw\Cms\PageCreator;

class PagesRepository extends EloquentRepository implements PagesRepositoryInterface
{
    private $creator;
    public function __construct(PageCreator $creator)
    {
        $this->creator = $creator;
    }

    public $model = '\Page';

    public function update($input)
    {

    }

    public function slug($slug)
    {
        return $this->make()->where('slug', $slug)->first();
    }

    public function create($input)
    {
        $page = new \Page();

        $filesnames = [];

        $page->title = $input['title'];
        $page->slug = \Str::slug($input['title']);
        $page->published = $input['published'];
        $page->author = $input['author'];
        $page->content = ' ';
        $page->audience_type_id = isset($input['audience_type']) ? $input['audience_type'] : 1;
        $page->save();

        for($i = 1; $i < 5; $i++) {
            $file = \Input::hasFile('image'.$i) ? \Input::file('image'.$i) : null;
            if(is_null($file)) break;
            if(!$file->isValid()) continue;
            $dir = $this->makeUploadDirectory();
            $file->move($dir[0], $file->getClientOriginalName());
            $filesnames[$i] = $dir[1] . '/' . $file->getClientOriginalName();
        }

        $page->content = $this->creator->create(\Input::get('content'), $filesnames);
        return $page->save();
    }

    private function makeUploadDirectory()
    {
        $month = date('m');
        $year = date('Y');
        $path = '/uploads/cms/'.$year.'/'.$month;
        $directory = public_path().$path;
        if(! \File::isDirectory($directory)) {
            \File::makeDirectory($directory, 755, true);
        }
        return [$directory, $path];
    }

    public static function orphaned()
    {
        return \Page::where('menu_id', null)->get();
    }
} 
