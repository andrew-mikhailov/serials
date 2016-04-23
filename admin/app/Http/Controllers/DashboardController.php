<?php

namespace App\Http\Controllers;

use Share\Episode;
use Share\Image;
use Share\Slider;
use Share\Video;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use \Share\User;
use \Share\Asset;
use \Share\Tag;
use \Share\Genre;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DashboardController extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('dashboard.pages.index');
    }


    public function assetList()
    {
        $assets = Asset::orderBy('id', 'desc')->get();

        return view('dashboard.pages.asset.asset_list', ['assets' => $assets]);
    }

    public function assetCreate(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
          'title' => 'required|max:255',
          'original_title' => 'required',
          'plot' => 'required',
          'image_id' => 'required',
          'slider_id' => 'required',
          'description' => 'required',
          'start_date' => 'required',
          'end_date' => 'required',
          'tags' => 'required',
          'genres' => 'required',
          'body' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/assets/create')
              ->withErrors($validator)
              ->withInput();
        }
        $asset = new Asset();
        $asset->title = $input['title'];
        $asset->original_title = $input['original_title'];
        $asset->plot = $input['plot'];
        $asset->image_id = $input['image_id'];
        $asset->slider_id = $input['slider_id'];
        $asset->description = $input['description'];
        $asset->start_date = $input['start_date'];
        $asset->end_date = $input['end_date'];
        $asset->body = $input['body'];
        $asset->user_id = $request->user()->id;
        $asset->save();

        $asset->tags()->sync($input['tags']);
        $asset->genre()->sync($input['genres']);

        return redirect('/admin/dashboard/assets');
    }

    public function showAssetCreate()
    {
        $tags = Tag::orderBy('id', 'desc')->get();
        $genres = Genre::orderBy('id', 'desc')->get();
        $previews = Image::where(['type' => 'preview'])->get();
        $sliders = Slider::where(['type' => 'asset'])->get();

        $tags_options = [];
        foreach ($tags as $tag) {
            $tags_options[$tag->id] = $tag->name;
        }
        $genre_options = [];
        foreach ($genres as $genre) {
            $genre_options[$genre->id] = $genre->name;
        }
        $previews_options = [];
        foreach ($previews as $preview) {
            $previews_options[$preview->id] = $preview->title;
        }
        $sliders_options = [];
        foreach ($sliders as $slider) {
            $sliders_options[$slider->id] = $slider->title;
        }

        return view('dashboard.pages.asset.asset_create',
          [
            'tags' => $tags_options,
            'genres' => $genre_options,
            'previews' => $previews_options,
            'sliders' => $sliders_options,
          ]
        );
    }

    public function showAssetEdit(Request $request, $id)
    {
        $asset = Asset::find($id);
        $current_tags = $asset->tags()->get();
        $tags = Tag::all();
        $selected_tags = [];
        $tags_list = [];
        $current_genres = $asset->genre()->get();
        $genres = Genre::all();
        $selected_genres = [];
        $genre_list = [];
        $previews = Image::where(['type' => 'preview'])->get();
        $selected_preview = $asset->images()->get();
        $preview_list = [];
        $sliders = Slider::where(['type' => 'asset'])->get();
        $selected_slider = $asset->slider()->get();
        $slider_list = [];

        foreach ($current_tags as $current_tag) {
            $selected_tags[$current_tag->id] = $current_tag->id;
        }
        foreach ($tags as $tag) {
            $tags_list[$tag->id] = $tag->name;
        }
        foreach ($current_genres as $current_genre) {
            $selected_genres[$current_genre->id] = $current_genre->id;
        }
        foreach ($genres as $genre) {
            $genre_list[$genre->id] = $genre->name;
        }
        foreach ($previews as $preview) {
            $preview_list[$preview->id] = $preview->title;
        }
        foreach ($sliders as $slider) {
            $slider_list[$slider->id] = $slider->title;
        }

        return view('dashboard.pages.asset.asset_edit', [
            'asset' => $asset,
            'tags_list' => $tags_list,
            'selected_tags' => $selected_tags,
            'genres_list' => $genre_list,
            'selected_genres' => $selected_genres,
            'previews_list' => $preview_list,
            'selected_preview' => $selected_preview,
            'slider_list' => $slider_list,
            'selected_slider' => $selected_slider,
          ]
        );
    }

    public function assetDelete(Request $request, $id)
    {
        $asset = Asset::find($id);
        $asset->delete();

        return redirect('/admin/dashboard/assets');
    }

    public function assetEdit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
          'title' => 'required|max:255',
          'original_title' => 'required|max:255',
          'plot' => 'required',
          'image_id' => 'required',
          'slider_id' => 'required',
          'description' => 'required',
          'start_date' => 'required',
          'end_date' => 'required',
          'tags' => 'required',
          'genres' => 'required',
          'body' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/assets/create')
              ->withErrors($validator)
              ->withInput();
        }

        $asset = Asset::find($id);
        $asset->title = $input['title'];
        $asset->original_title = $input['original_title'];
        $asset->plot = $input['plot'];
        $asset->image_id = $input['image_id'];
        $asset->slider_id = $input['slider_id'];
        $asset->description = $input['description'];
        $asset->start_date = $input['start_date'];
        $asset->end_date = $input['end_date'];
        $asset->body = $input['body'];
        $asset->user_id = $request->user()->id;
        $asset->tags()->detach();
        $asset->tags()->sync($input['tags']);
        $asset->genre()->detach();
        $asset->genre()->sync($input['genres']);

        $asset->save();

        return redirect('/admin/dashboard/assets');
    }


    public function userList()
    {
        $users = User::orderBy('name', 'desc')->get();

        return view('dashboard.pages.user.user_list', ['users' => $users]);
    }

    public function showUserEdit($id)
    {
        $user = User::find($id);

        return view('dashboard.pages.user.user_edit', ['user' => $user]);
    }

    public function userEdit(Request $request, $id)
    {
        $user = User::find($id);
        $input = $request->all();

        $validator = Validator::make($request->all(), [
          'name' => 'required|unique:users|max:255',
          'email' => 'required|unique:users|max:255|email',
          'password' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/users/' . $id . '/edit')
              ->withErrors($validator)
              ->withInput();
        }

        if (isset($input['name'])) {
            $user->name = $input['name'];
        }
        if (isset($input['email'])) {
            $user->email = $input['email'];
        }
        if (isset($input['password'])) {
            $user->password = bcrypt($input['password']);
        }
        $user->save();

        return view('dashboard.pages.user.user_edit',
          ['user' => $user, 'message' => 'User has been save']);
    }

    public function showUserCreate()
    {
        return view('dashboard.pages.user.user_create');
    }

    public function userCreate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
          'name' => 'required|unique:users|max:255',
          'email' => 'required|unique:users|max:255|email',
          'password' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/users/create')
              ->withErrors($validator)
              ->withInput();
        }

        $user = new User();
        if (isset($input['name'])) {
            $user->name = $input['name'];
        }
        if (isset($input['email'])) {
            $user->email = $input['email'];
        }
        if (isset($input['password'])) {
            $user->password = bcrypt($input['password']);
        }
        $user->save();

        return redirect('/admin/dashboard/users');
    }


    public function tagsList()
    {
        $tags = Tag::orderBy('id', 'desc')->get();

        return view('dashboard.pages.tag.tag_list', ['tags' => $tags]);
    }

    public function tagCreate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
          'name' => 'required|unique:users|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/tags/create')
              ->withErrors($validator)
              ->withInput();
        }

        $tag = new Tag();
        if (isset($input['name'])) {
            $tag->name = $input['name'];
        }

        $tag->save();

        return redirect('/admin/dashboard/tags');
    }

    public function showTagCreate()
    {
        return view('dashboard.pages.tag.tag_create');
    }

    public function showTagEdit(Request $request, $id)
    {
        $tag = Tag::find($id);

        return view('dashboard.pages.tag.tag_edit', ['tag' => $tag]);
    }

    public function tagEdit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
          'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/tags/create')
              ->withErrors($validator)
              ->withInput();
        }

        $tag = Tag::find($id);
        if (isset($input['name'])) {
            $tag->name = $input['name'];
        }

        $tag->save();

        return redirect('/admin/dashboard/tags');
    }


    public function genreList(Request $request)
    {
        $genres = Genre::orderBy('id', 'desc')->get();

        return view('dashboard.pages.genre.genre_list', ['genres' => $genres]);
    }

    public function showGenreCreate()
    {
        return view('dashboard.pages.genre.genre_create');
    }

    public function genreCreate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
          'name' => 'required|unique:users|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/genres/create')
              ->withErrors($validator)
              ->withInput();
        }

        $genre = new Genre();
        if (isset($input['name'])) {
            $genre->name = $input['name'];
        }

        $genre->save();

        return redirect('/admin/dashboard/genres');
    }

    public function genreEdit(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
          'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/genres/create')
              ->withErrors($validator)
              ->withInput();
        }
        $genre = Genre::find($id);
        if (isset($input['name'])) {
            $genre->name = $input['name'];
        }
        $genre->save();

        return redirect('/admin/dashboard/genres');
    }

    public function showGenreEdit(Request $request, $id)
    {
        $genre = Genre::find($id);

        return view('dashboard.pages.genre.genre_edit', ['genre' => $genre]);
    }


    public function imageList()
    {
        $images = Image::orderBy('id', 'desc')->get();

        foreach ($images as $image) {
            $image->path = public_path() . '//' . ($image->path);
        }

        return view('dashboard.pages.image.image_list', ['images' => $images]);
    }

    public function showImageCreate()
    {
        return view('dashboard.pages.image.image_create',
          ['types' => Image::getTypes()]
        );
    }

    public function showImageEdit(Request $request, $id)
    {
        $image = Image::find($id);

        $image->src = asset(Storage::disk($image->type)->url($image->path));

        return view('dashboard.pages.image.image_edit',
          ['image' => $image, 'types' => Image::getTypes()]
        );
    }

    public function imageDelete(Request $request, $id)
    {
        $image = Image::find($id);
        $image->delete();

        return redirect('/admin/dashboard/images');
    }

    public function imageEdit(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
          'title' => 'required|max:255',
          'type' => 'required|max:255',
          'path' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/images/create')
              ->withErrors($validator)
              ->withInput();
        }

        $file = $request->file('path');
        $extension = $file->getClientOriginalExtension();
        Storage::disk($input['type'])->put($file->getFilename() . '.' . $extension,
          File::get($file));

        $image = Image::find($id);
        $image->title = $input['title'];
        $image->type = $input['type'];
        $image->path = $file->getFilename() . '.' . $extension;

        $image->save();

        return redirect('/admin/dashboard/images');
    }

    public function imageCreate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
          'title' => 'required|max:255',
          'type' => 'required|max:255',
          'path' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/images/create')
              ->withErrors($validator)
              ->withInput();
        }

        $file = $request->file('path');
        $extension = $file->getClientOriginalExtension();
        Storage::disk($input['type'])->put($file->getFilename() . '.' . $extension,
          File::get($file));

        $image = new Image();
        $image->title = $input['title'];
        $image->type = $input['type'];
        $image->path = $file->getFilename() . '.' . $extension;

        $image->save();

        return redirect('/admin/dashboard/images');
    }


    public function videoList()
    {
        $videos = Video::orderBy('id', 'desc')->get();

        return view('dashboard.pages.video.video_list', ['videos' => $videos]);
    }

    public function showVideoCreate()
    {
        return view('dashboard.pages.video.video_create',
          ['quality' => Video::getQuality()]
        );
    }

    public function showVideoEdit(Request $request, $id)
    {
        $video = Video::find($id);

        return view('dashboard.pages.video.video_edit',
          ['video' => $video, 'quality' => Video::getQuality()]
        );
    }

    public function videoEdit(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
          'title' => 'required|max:255',
          'extension' => 'required|max:3',
          'path' => 'required',
          'quality' => 'required|max:3',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/videos/create')
              ->withErrors($validator)
              ->withInput();
        }

        $video = Video::find($id);
        $video->title = $input['title'];
        $video->extension = $input['extension'];
        $video->quality = $input['quality'];

        $file_content = file_get_contents($request->file('path')->getRealPath());
        Storage::disk('videos')->put($_FILES['path']['name'], $file_content);
        $video->path = $_FILES['path']['name'];
        $video->save();

        return redirect('/admin/dashboard/videos');
    }

    public function videoCreate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
          'title' => 'required|max:255',
          'extension' => 'required|max:3',
          'path' => 'required',
          'quality' => 'required|max:3',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/videos/create')
              ->withErrors($validator)
              ->withInput();
        }

        $video = new Video();
        $video->title = $input['title'];
        $video->extension = $input['extension'];
        $video->quality = $input['quality'];

        $file_content =
          file_get_contents($request->file('path')->getRealPath());
        Storage::disk('videos')->put($_FILES['path']['name'], $file_content);
        $video->path = $_FILES['path']['name'];
        $video->save();

        return redirect('/admin/dashboard/videos');
    }


    public function episodeList(Request $request)
    {
        $episodes = Episode::orderBy('id', 'desc')->get();

        return view('dashboard.pages.episode.episode_list',
          ['episodes' => $episodes]);
    }

    public function showEpisodeCreate()
    {
        $images = Image::where(['type' => 'poster'])->get();
        $videos = Video::orderBy('id', 'desc')->get();
        $assets = Asset::orderBy('id', 'desc')->get();

        $images_options = [];
        foreach ($images as $image) {
            $images_options[$image->id] = $image->title;
        }
        $videos_options = [];
        foreach ($videos as $video) {
            $videos_options[$video->id] = $video->title;
        }
        $assets_options = [];
        foreach ($assets as $asset) {
            $assets_options[$asset->id] = $asset->title;
        }

        return view(
          'dashboard.pages.episode.episode_create',
          [
            'images' => $images_options,
            'videos' => $videos_options,
            'assets' => $assets_options,
          ]
        );
    }

    public function showEpisodeEdit(Request $request, $id)
    {
        $episode = Episode::find($id);

        $current_image = Image::find($episode->image_id);
        $images = Image::where(['type' => 'poster'])->get()->toArray();
        $selected_images = [];
        $images_list = [];

        $current_video_id = Video::find($episode->video_id);
        $video_ids = Video::all();
        $selected_video = [];
        $video_list = [];

        $current_asset_id = Asset::find($episode->asset_id);
        $asset_ids = Asset::all();
        $selected_asset = [];
        $asset_list = [];

        $selected_images[$current_image->id] = $current_image->id;

        foreach ($images as $image) {
            $images_list[$image['id']] = $image['title'];
        }

        $selected_video[$current_video_id->id] = $current_video_id->id;

        foreach ($video_ids as $video_id) {
            $video_list[$video_id->id] = $video_id->title;
        }

        $selected_asset[$current_asset_id->id] = $current_asset_id->id;

        foreach ($asset_ids as $asset_id) {
            $asset_list[$asset_id->id] = $asset_id->title;
        }

        return view('dashboard.pages.episode.episode_edit', [
            'episode' => $episode,
            'images_list' => $images_list,
            'selected_images' => $selected_images,
            'video_list' => $video_list,
            'selected_video' => $selected_video,
            'asset_list' => $asset_list,
            'selected_asset' => $selected_asset
          ]
        );
    }


    public function episodeDelete(Request $request, $id)
    {
        $episode = Episode::find($id);
        $episode->delete();

        return redirect('/admin/dashboard/episodes');
    }

    public function episodeEdit(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
          'season_number' => 'required|numeric|max:255|min:1',
          'episode_number' => 'required|numeric|max:255|min:1',
          'asset_id' => 'required|max:255|min:1',
          'title' => 'required|max:255',
          'description' => 'required|max:255',
          'image_id' => 'required',
          'video_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/episodes/create')
              ->withErrors($validator)
              ->withInput();
        }

        $episode = Episode::find($id);
        $episode->season_number = $input['season_number'];
        $episode->episode_number = $input['episode_number'];
        $episode->title = $input['title'];
        $episode->description = $input['description'];
        $episode->asset_id = $input['asset_id'];
        $episode->image_id = $input['image_id'];
        $episode->video_id = $input['video_id'];
        $episode->save();

        return redirect('/admin/dashboard/episodes');
    }

    public function episodeCreate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
          'season_number' => 'required|numeric|max:255|min:1',
          'episode_number' => 'required|numeric|max:255|min:1',
          'title' => 'required|max:255',
          'description' => 'required|max:255',
          'asset_id' => 'required|max:255|min:1',
          'image_id' => 'required',
          'video_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/episodes/create')
              ->withErrors($validator)
              ->withInput();
        }

        $episode = new Episode();
        $episode->season_number = $input['season_number'];
        $episode->episode_number = $input['episode_number'];
        $episode->title = $input['title'];
        $episode->description = $input['description'];
        $episode->asset_id = $input['asset_id'];
        $episode->image_id = $input['image_id'];
        $episode->video_id = $input['video_id'];
        $episode->save();

        return redirect('/admin/dashboard/episodes');
    }


    public function sliderList()
    {
        $sliders = Slider::orderBy('id', 'desc')->get();

        return view('dashboard.pages.slider.slider_list', ['sliders' => $sliders]);
    }

    public function showSliderCreate()
    {
        $slides = Image::where(['type' => 'slide'])->get()->toArray();

        $slides_options = [];
        foreach ($slides as $slide) {
            $slides_options[$slide['id']] = $slide['title'];
        }


        return view('dashboard.pages.slider.slider_create',
          ['slides' => $slides_options, 'types' => Slider::getTypes()]
        );
    }

    public function showSliderEdit(Request $request, $id)
    {
        $slider = Slider::find($id);
        $slides = Image::where(['type' => 'slide'])->get()->toArray();
        $slides_options = [];
        foreach ($slides as $slide) {
            $slides_options[$slide['id']] = $slide['title'];
        }
        $selected_slides = $slider->slides()->get()->toArray();
        $selected_slides_options = [];
        foreach ($selected_slides as $selected_slide) {
            $selected_slides_options[$selected_slide['id']] = $selected_slide['id'];
        }

        return view('dashboard.pages.slider.slider_edit',
          [
            'slider' => $slider,
            'slides_options' => $slides_options,
            'selected_slides_options' => $selected_slides_options,
            'types' => Slider::getTypes()
          ]
        );
    }

    public function sliderEdit(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
          'title' => 'required|max:255',
          'type' => 'required|max:255',
          'slides' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/slides/create')
              ->withErrors($validator)
              ->withInput();
        }

        $slider = Slider::find($id);
        $slider->title = $input['title'];
        $slider->type = $input['type'];
        $slider->save();
        $slider->slides()->detach();
        $slider->slides()->sync($input['slides']);

        return redirect('/admin/dashboard/sliders');
    }

    public function sliderCreate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
          'title' => 'required|max:255',
          'type' => 'required|max:255',
          'slides' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/dashboard/slides/create')
              ->withErrors($validator)
              ->withInput();
        }

        $slider = new Slider();
        $slider->title = $input['title'];
        $slider->type = $input['type'];
        $slider->save();
        $slider->slides()->sync($input['slides']);

        $slider->save();

        return redirect('/admin/dashboard/sliders');
    }
}