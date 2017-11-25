/* tslint:disable:max-classes-per-file */

declare namespace TYPO3 {
  export namespace CMS {
    export namespace Backend {
      export class AjaxDataHandler {
        public process(parameters: any): Promise<any>;
      }
      export class Icons {
        public readonly sizes: { [key: string]: string };
        public getIcon(identifier: string, size: string): JQueryPromise<any>;
      }
      export class Wizard {
        public addSlide(identifier: string, title: string, content: string, severity: number, callback?: Function): Wizard;
        public addFinalProcessingSlide(callback: Function): JQueryPromise<any>;
        public unlockNextStep(): JQuery;
        public lockNextStep(): JQuery;
        public dismiss(): void;
        public show(): JQuery;
        public getComponent(): JQuery;
      }
    }
  }
}

declare module 'TYPO3/CMS/Backend/AjaxDataHandler' {
  export = new TYPO3.CMS.Backend.AjaxDataHandler();
}

declare module 'TYPO3/CMS/Backend/Icons' {
  export = new TYPO3.CMS.Backend.Icons();
}

declare module 'TYPO3/CMS/Backend/Wizard' {
  export = new TYPO3.CMS.Backend.Wizard();
}
