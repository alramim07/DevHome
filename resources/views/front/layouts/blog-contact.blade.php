<section id="contact" class="ke eg mm mn">
  <div class="a">
    <div class="-ud-mx-4 sb wd">
      <div class="oc tf">
        <div class="animate_top la ib nd yg">
          <h2 class="pa yg bh lh sh zh mj ml rl _n ao">
            Need Help? Contact With Us
          </h2>
          <p class="mh uh ol sl">
            Submit Your Message, We will be with you as soon as possible...
          </p>
        </div>
      </div>
    </div>

    <div class="-ud-mx-4 sb">
      <div class="animate_top oc tf">
        <div class="ii ne ve ye gj k la oc nd cf hj xf dg fk gk gl om pm">
          <div x-data="{
    showSuccess: false,
    successMsg: '',
    loading: false,
    errors: {},
    submitForm(e) {
        this.loading = true;
        this.errors = {};
        const form = e.target;

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(async res => {
            const data = await res.json();

            if (res.ok) {
                this.successMsg = data.message;
                this.showSuccess = true;
                form.reset();
                setTimeout(() => this.showSuccess = false, 5000);
            } else if (res.status === 422) {
                this.errors = data.errors;
            } else {
                alert(data.message || 'Something went wrong');
            }
        })
        .catch(err => console.error('Fetch Error:', err))
        .finally(() => this.loading = false);
    }
}">
            @include('front.partials.messages')

            <form action="{{ route('contact.submit') }}" @submit.prevent="submitForm($event)">
              @csrf
              <div class="-ud-mx-4 sb wd">
                <div class="oc tf bl/2">
                  <div class="kb">
                    <label for="firstName" class="zh mj pa qb mh">First Name</label>
                    <input id="firstName" type="text" name="first_name" required placeholder="Enter your first name"
                      class="oe ve ye gj cj oj fi oc df cg dg di ki">
                    <template x-if="errors.first_name">
                      <div class="text-red-500 text-sm mt-1" x-text="errors.first_name[0]"></div>
                    </template>
                  </div>
                </div>
                <div class="oc tf bl/2">
                  <div class="kb">
                    <label for="lastName" class="zh mj pa qb mh">Last Name</label>
                    <input id="lastName" type="text" name="last_name" required placeholder="Enter your last Name"
                      class="oe ve ye gj cj oj fi oc df cg dg di ki">
                    <template x-if="errors.last_name">
                      <div class="text-red-500 text-sm mt-1" x-text="errors.last_name[0]"></div>
                    </template>
                  </div>
                </div>
                <div class="oc tf bl/2">
                  <div class="kb">
                    <label for="email" class="zh mj pa qb mh">Email</label>
                    <input id="email" type="email" name="email" required placeholder="Enter your Email"
                      class="oe ve ye gj cj oj fi oc df cg dg di ki">
                    <template x-if="errors.email">
                      <div class="text-red-500 text-sm mt-1" x-text="errors.email[0]"></div>
                    </template>
                  </div>
                </div>
                <div class="oc tf bl/2">
                  <div class="kb">
                    <label for="number" class="zh mj pa qb mh">Phone number</label>
                    <input id="number" type="text" name="phone_number" placeholder="Enter your number"
                      class="oe ve ye gj cj oj fi oc df cg dg di ki">
                    <template x-if="errors.phone_number">
                      <div class="text-red-500 text-sm mt-1" x-text="errors.phone_number[0]"></div>
                    </template>
                  </div>
                </div>
                <div class="oc tf">
                  <div class="kb">
                    <label for="subject" class="zh mj pa qb mh">Subject</label>
                    <input id="subject" type="text" name="subject" required
                      value="{{ old('subject', $blog->title) }}"
                      class="oe ve ye gj cj oj fi oc df cg dg di ki">
                    <template x-if="errors.subject">
                      <div class="text-red-500 text-sm mt-1" x-text="errors.subject[0]"></div>
                    </template>
                  </div>
                </div>
                <div class="oc tf">
                  <div class="kb">
                    <label for="message" class="zh mj pa qb mh">What are you looking for?</label>
                    <textarea id="message" name="message" required placeholder="Type your message here" rows="6"
                      class="cj oj fi oc ud oe ve ye gj df cg dg di ki"></textarea>
                    <template x-if="errors.message">
                      <div class="text-red-500 text-sm mt-1" x-text="errors.message[0]"></div>
                    </template>
                  </div>
                </div>
                <div class="oc tf">
                  <div class="yg">
                    <button class="bf ub xd yd oe we ze rf jg jh nh _h xi" type="submit" :disabled="loading">
                      <span x-show="!loading">Send Message</span>
                      <span x-show="loading">Sending...</span>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
