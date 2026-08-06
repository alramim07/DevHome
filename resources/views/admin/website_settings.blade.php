@extends('admin.layouts.app')
@section('content')
  <div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded h-100 p-4">
      <h2 class="mb-4">Website Settings</h2>
      <form action="{{ route('website.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
      <div class="mb-3">
        <label for="formFileMultiple" class="form-label">Fav Icon</label>
        <input class="form-control form-control-sm bg-dark" name="fav_icon" id="formFileSm" type="file">
      </div>
      <div class="mb-3">
        <label for="formFileMultiple" class="form-label">Logo For Light Theme</label>
        <input class="form-control form-control-sm bg-dark" name="logo" id="formFileSm" type="file">
      </div>
      <div class="mb-3">
        <label for="formFileSm" class="form-label">Logo For Dark Theme</label>
        <input class="form-control form-control-sm bg-dark"  name="logo_dark" id="formFileSm" type="file">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Hero SVG 1</label>
        <input class="form-control form-control-lg bg-dark"  name="hero_svg1" id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Hero SVG 2</label>
        <input class="form-control form-control-lg bg-dark" name="hero_svg2"  id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Hero SVG 3</label>
        <input class="form-control form-control-lg bg-dark" name="hero_svg3"  id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Hero SVG 4</label>
        <input class="form-control form-control-lg bg-dark" name="hero_svg4"  id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Tagline</label>
        <input class="form-control form-control-lg bg-dark" name="tagline" value="{{ $settings->tagline ?? '' }}" id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Tagline Heading</label>
        <input class="form-control form-control-lg bg-dark" name="tagline_h" value="{{ $settings->tagline_h ?? '' }}" id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Tagline Description</label>
        <input class="form-control form-control-lg bg-dark" name="tagline_p" value="{{ $settings->tagline_p ?? '' }}" id="formFileLg" type="text">
      </div>
      <div class="mb-3">
        <label for="formFileMultiple" class="form-label">About Us 1st Image</label>
        <input class="form-control form-control-sm bg-dark" name="about_us_img1"  id="formFileSm" type="file">
      </div>
      <div class="mb-3">
        <label for="formFileSm" class="form-label">About Us 2nd Image</label>
        <input class="form-control form-control-sm bg-dark" name="about_us_img2"  id="formFileSm" type="file">
      </div>
      <div>
        <label for="formFileLg" class="form-label">What We Build Section</label>
        <input class="form-control form-control-lg bg-dark" name="what_we_build" value="{{ $settings->what_we_build ?? '' }}"  id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Why Dev Home</label>
        <input class="form-control form-control-lg bg-dark" name="why_dev_home" value="{{ $settings->why_dev_home ?? '' }}"  id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">How We Work</label>
        <input class="form-control form-control-lg bg-dark" name="how_we_work" value="{{ $settings->how_we_work ?? '' }}"  id="formFileLg" type="text">
      </div>
      <div>
        <label for="formFileLg" class="form-label">Footer Description</label>
        <input class="form-control form-control-lg bg-dark" name="footer_des" value="{{ $settings->footer_des ?? '' }}"  id="formFileLg" type="text">
      </div>
      <div class="add-btn">
        <button type="submit" class="btn btn-outline-success m-2">Update Website</button>
      </div>
    </div>
    </form>
  </div>

@endsection
