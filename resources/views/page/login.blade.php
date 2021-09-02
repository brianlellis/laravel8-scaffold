<div class="body">
  <p>Login To Your Account</p>
  <div class="form">
    <form method="POST" action="{{ route('rapyd.user.auth') }}">
      @csrf
      <div class="form-group">
        <label for="email">Email</label>
        <input name="email" type="text" value="{{ old('email') }}"/>
        @error('email')
          <small>{{ $message }}</small>
        @enderror
      </div>

      <div class="form-group">
        <label for="password">Password</label>
          <input name="password" type="password"/>
          @error('password')
            <small>{{ $message }}</small>
          @enderror
      </div>
      <div class="form-group">
        <button class="submit">Login</button>
      </div>
    </form>
  </div>

  <div class="cta">
    <a href="/login?forgotpassword=true">Forgot your password?</a>
  </div>
</div>