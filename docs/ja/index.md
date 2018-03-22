# Nazg Frameworkについて

Nazg FrameworkはPSR-7, PSR-11, PSR-15に対応したフレームワークで、  
ミドルウェアコンポーネントの [heredity](https://github.com/nazg-hack/heredity) を中核に構築されています。  

Web APIや小規模なWeb開発向けフレームワークで、[ADR](http://paul-m-jones.com/archives/5970) パターンで実装することを想定していますが、  
アプリケーション開発に制限を与えるようなものはなく、  
ディレクトリ構成などは決まっていません。  
アプリケーションや開発チーム合わせて自由に利用してください。  

上記のPSRに準拠したライブラリであれば、  
自由に入れ替えることができますが、HHVM/Hackに対応したライブラリでなければなりません  

## Nazg Frameworkの機能を理解する

 - ルーティング
 - [DI Container / hh-container](using-di-container.md)
 - バリデーション
 - CORSミドルウェア
 
