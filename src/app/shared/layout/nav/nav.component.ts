import { Component, Input, OnInit } from '@angular/core';

// import { AuthService } from '../../../lib/auth/auth.service';

@Component({
  selector: 'app-nav',
  templateUrl: './nav.component.html',
  styleUrls: ['./nav.component.css']
})
export class NavComponent implements OnInit {

  @Input()
  title: string;
  navbarCollapsed = true;

  constructor(
    // private authService: AuthService
  ) {
  }

  ngOnInit() {
  }

  isLoggedIn() {

    return true; // this.authService.isLoggedIn();
  }

}
