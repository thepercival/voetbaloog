import { Injectable } from "@angular/core";
import 'rxjs/add/operator/toPromise';
import { AngularFireAuth } from '@angular/fire/auth';
import { auth } from 'firebase/app';
import * as firebase from 'firebase/app';
import { UserService } from './user.service';

@Injectable()
export class AuthService {

  constructor(
    public afAuth: AngularFireAuth,
    public userService: UserService
  ) { }

  doGoogleLogin() {
    return new Promise<any>((resolve, reject) => {
      let provider = new auth.GoogleAuthProvider();
      provider.addScope('profile');
      provider.addScope('email');
      this.afAuth.signInWithPopup(provider)
        .then(res => {
          resolve(res);
        }, err => {
          console.log(err);
          reject(err);
        })
    })
  }



  doRegister(value) {
    return new Promise<any>((resolve, reject) => {
      this.afAuth.createUserWithEmailAndPassword(value.email, value.password)
        .then(res => {
          // this.userService.getCurrentUser()
          //   .then((user) => {
          //     user.sendEmailVerification().then((user) => {
          //       return resolve(false);
          //     }, err => {
          //       return resolve(true);
          //     });
          //   }, err => {
          //     return resolve(true);
          //   });

          firebase.auth().currentUser.sendEmailVerification();
          // console.log(res);
          resolve(res);
        }, err => reject(err))
    })
  }

  doLogin(value) {
    return new Promise<any>((resolve, reject) => {
      this.afAuth.signInWithEmailAndPassword(value.email, value.password)
        .then(res => {
          resolve(res);
        }, err => reject(err))
    })
  }

  doLogout() {
    return new Promise((resolve, reject) => {
      if (this.afAuth.currentUser) {
        this.afAuth.signOut();
        resolve();
      }
      else {
        reject();
      }
    });
  }


}
